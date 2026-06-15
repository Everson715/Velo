"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.MatchService = void 0;
const common_1 = require("@nestjs/common");
const prisma_service_1 = require("../prisma/prisma.service");
const client_1 = require("@prisma/client");
let MatchService = class MatchService {
    prisma;
    constructor(prisma) {
        this.prisma = prisma;
    }
    async requestTrip(createDto) {
        try {
            const trip = await this.prisma.trip.create({
                data: {
                    ...createDto,
                    status: client_1.TripStatus.SEARCHING,
                },
            });
            return trip;
        }
        catch (error) {
            throw new common_1.BadRequestException('Failed to request trip');
        }
    }
    async getTripStatus(tripId) {
        const trip = await this.prisma.trip.findUnique({
            where: { id: tripId },
            select: { status: true, driverId: true },
        });
        if (!trip) {
            throw new common_1.NotFoundException('Trip not found');
        }
        return trip;
    }
    async cancelTrip(tripId) {
        try {
            return await this.prisma.$transaction(async (tx) => {
                const trip = await tx.trip.findUnique({ where: { id: tripId } });
                if (!trip) {
                    throw new common_1.NotFoundException('Trip not found');
                }
                if (trip.status === client_1.TripStatus.CANCELLED || trip.status === client_1.TripStatus.COMPLETED) {
                    throw new common_1.BadRequestException('Trip cannot be cancelled');
                }
                return await tx.trip.update({
                    where: { id: tripId },
                    data: { status: client_1.TripStatus.CANCELLED },
                });
            });
        }
        catch (error) {
            if (error instanceof common_1.NotFoundException || error instanceof common_1.BadRequestException) {
                throw error;
            }
            throw new common_1.BadRequestException('Failed to cancel trip');
        }
    }
    async getAvailableTrips() {
        return this.prisma.trip.findMany({
            where: { status: client_1.TripStatus.SEARCHING },
            orderBy: { createdAt: 'desc' },
        });
    }
    async acceptMatch(acceptDto) {
        const { tripId, driverId } = acceptDto;
        try {
            return await this.prisma.$transaction(async (tx) => {
                const trip = await tx.trip.findUnique({ where: { id: tripId } });
                if (!trip) {
                    throw new common_1.NotFoundException('Trip not found');
                }
                if (trip.status !== client_1.TripStatus.SEARCHING) {
                    throw new common_1.ConflictException('Trip is no longer available');
                }
                const { count } = await tx.trip.updateMany({
                    where: {
                        id: tripId,
                        status: client_1.TripStatus.SEARCHING,
                    },
                    data: {
                        status: client_1.TripStatus.MATCHED,
                        driverId,
                    },
                });
                if (count === 0) {
                    throw new common_1.ConflictException('Trip was accepted by another driver');
                }
                const attempt = await tx.matchAttempt.findUnique({
                    where: { tripId_driverId: { tripId, driverId } }
                });
                if (attempt) {
                    await tx.matchAttempt.update({
                        where: { id: attempt.id },
                        data: { status: client_1.AttemptStatus.ACCEPTED },
                    });
                }
                return tx.trip.findUnique({ where: { id: tripId } });
            });
        }
        catch (error) {
            if (error instanceof common_1.NotFoundException || error instanceof common_1.ConflictException) {
                throw error;
            }
            throw new common_1.BadRequestException('Failed to accept match');
        }
    }
    async declineMatch(declineDto) {
        const { tripId, driverId } = declineDto;
        try {
            return await this.prisma.$transaction(async (tx) => {
                const attempt = await tx.matchAttempt.findUnique({
                    where: { tripId_driverId: { tripId, driverId } },
                });
                if (!attempt) {
                    throw new common_1.NotFoundException('Match attempt not found');
                }
                return await tx.matchAttempt.update({
                    where: { id: attempt.id },
                    data: { status: client_1.AttemptStatus.DECLINED },
                });
            });
        }
        catch (error) {
            if (error instanceof common_1.NotFoundException) {
                throw error;
            }
            throw new common_1.BadRequestException('Failed to decline match');
        }
    }
    async arriveTrip(tripId) {
        return this.updateTripStatus(tripId, client_1.TripStatus.MATCHED, client_1.TripStatus.ARRIVED, 'Only matched trips can be marked as arrived');
    }
    async startTrip(tripId) {
        return this.updateTripStatus(tripId, client_1.TripStatus.ARRIVED, client_1.TripStatus.IN_PROGRESS, 'Only arrived trips can be started');
    }
    async completeTrip(tripId) {
        return this.updateTripStatus(tripId, client_1.TripStatus.IN_PROGRESS, client_1.TripStatus.COMPLETED, 'Only in-progress trips can be completed');
    }
    async updateTripStatus(tripId, currentStatus, newStatus, errorMsg) {
        try {
            return await this.prisma.$transaction(async (tx) => {
                const trip = await tx.trip.findUnique({ where: { id: tripId } });
                if (!trip) {
                    throw new common_1.NotFoundException('Trip not found');
                }
                if (trip.status !== currentStatus) {
                    throw new common_1.BadRequestException(errorMsg);
                }
                return await tx.trip.update({
                    where: { id: tripId },
                    data: { status: newStatus },
                });
            });
        }
        catch (error) {
            if (error instanceof common_1.NotFoundException || error instanceof common_1.BadRequestException) {
                throw error;
            }
            throw new common_1.BadRequestException('Failed to update trip status');
        }
    }
};
exports.MatchService = MatchService;
exports.MatchService = MatchService = __decorate([
    (0, common_1.Injectable)(),
    __metadata("design:paramtypes", [prisma_service_1.PrismaService])
], MatchService);
//# sourceMappingURL=match.service.js.map