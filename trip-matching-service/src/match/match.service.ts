import {
  BadRequestException,
  ConflictException,
  Injectable,
  NotFoundException,
} from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CreateMatchRequestDto } from './dto/create-match-request.dto';
import { AcceptMatchDto } from './dto/accept-match.dto';
import { DeclineMatchDto } from './dto/decline-match.dto';
import { AttemptStatus, TripStatus } from '@prisma/client';

@Injectable()
export class MatchService {
  constructor(private readonly prisma: PrismaService) {}

  async requestTrip(createDto: CreateMatchRequestDto) {
    try {
      const trip = await this.prisma.trip.create({
        data: {
          ...createDto,
          status: TripStatus.SEARCHING,
        },
      });
      return trip;
    } catch (error) {
      throw new BadRequestException('Failed to request trip');
    }
  }

  async getTripStatus(tripId: string) {
    const trip = await this.prisma.trip.findUnique({
      where: { id: tripId },
      select: { status: true, driverId: true },
    });
    if (!trip) {
      throw new NotFoundException('Trip not found');
    }
    return trip;
  }

  async cancelTrip(tripId: string) {
    try {
      return await this.prisma.$transaction(async (tx) => {
        const trip = await tx.trip.findUnique({ where: { id: tripId } });
        if (!trip) {
          throw new NotFoundException('Trip not found');
        }
        if (trip.status === TripStatus.CANCELLED || trip.status === TripStatus.COMPLETED) {
          throw new BadRequestException('Trip cannot be cancelled');
        }
        
        return await tx.trip.update({
          where: { id: tripId },
          data: { status: TripStatus.CANCELLED },
        });
      });
    } catch (error) {
      if (error instanceof NotFoundException || error instanceof BadRequestException) {
        throw error;
      }
      throw new BadRequestException('Failed to cancel trip');
    }
  }

  async getAvailableTrips() {
    // Return all trips currently searching
    return this.prisma.trip.findMany({
      where: { status: TripStatus.SEARCHING },
      orderBy: { createdAt: 'desc' },
    });
  }

  async acceptMatch(acceptDto: AcceptMatchDto) {
    const { tripId, driverId } = acceptDto;

    try {
      return await this.prisma.$transaction(async (tx) => {
        const trip = await tx.trip.findUnique({ where: { id: tripId } });
        
        if (!trip) {
          throw new NotFoundException('Trip not found');
        }

        if (trip.status !== TripStatus.SEARCHING) {
          throw new ConflictException('Trip is no longer available');
        }

        // Conditional update to handle concurrency
        const { count } = await tx.trip.updateMany({
          where: {
            id: tripId,
            status: TripStatus.SEARCHING,
          },
          data: {
            status: TripStatus.MATCHED,
            driverId,
          },
        });

        if (count === 0) {
          throw new ConflictException('Trip was accepted by another driver');
        }

        // Update match attempts if they exist, otherwise ignore
        const attempt = await tx.matchAttempt.findUnique({
          where: { tripId_driverId: { tripId, driverId } }
        });
        
        if (attempt) {
          await tx.matchAttempt.update({
            where: { id: attempt.id },
            data: { status: AttemptStatus.ACCEPTED },
          });
        }

        return tx.trip.findUnique({ where: { id: tripId } });
      });
    } catch (error) {
      if (error instanceof NotFoundException || error instanceof ConflictException) {
        throw error;
      }
      throw new BadRequestException('Failed to accept match');
    }
  }

  async declineMatch(declineDto: DeclineMatchDto) {
    const { tripId, driverId } = declineDto;

    try {
      return await this.prisma.$transaction(async (tx) => {
        const attempt = await tx.matchAttempt.findUnique({
          where: { tripId_driverId: { tripId, driverId } },
        });

        if (!attempt) {
          throw new NotFoundException('Match attempt not found');
        }

        return await tx.matchAttempt.update({
          where: { id: attempt.id },
          data: { status: AttemptStatus.DECLINED },
        });
      });
    } catch (error) {
      if (error instanceof NotFoundException) {
        throw error;
      }
      throw new BadRequestException('Failed to decline match');
    }
  }

  async arriveTrip(tripId: string) {
    return this.updateTripStatus(tripId, TripStatus.MATCHED, TripStatus.ARRIVED, 'Only matched trips can be marked as arrived');
  }

  async startTrip(tripId: string) {
    return this.updateTripStatus(tripId, TripStatus.ARRIVED, TripStatus.IN_PROGRESS, 'Only arrived trips can be started');
  }

  async completeTrip(tripId: string) {
    return this.updateTripStatus(tripId, TripStatus.IN_PROGRESS, TripStatus.COMPLETED, 'Only in-progress trips can be completed');
  }

  private async updateTripStatus(tripId: string, currentStatus: TripStatus, newStatus: TripStatus, errorMsg: string) {
    try {
      return await this.prisma.$transaction(async (tx) => {
        const trip = await tx.trip.findUnique({ where: { id: tripId } });

        if (!trip) {
          throw new NotFoundException('Trip not found');
        }

        if (trip.status !== currentStatus) {
          throw new BadRequestException(errorMsg);
        }

        return await tx.trip.update({
          where: { id: tripId },
          data: { status: newStatus },
        });
      });
    } catch (error) {
      if (error instanceof NotFoundException || error instanceof BadRequestException) {
        throw error;
      }
      throw new BadRequestException('Failed to update trip status');
    }
  }
}
