import { Test, TestingModule } from '@nestjs/testing';
import { MatchService } from './match.service';
import { PrismaService } from '../prisma/prisma.service';
import { ConflictException, BadRequestException, NotFoundException } from '@nestjs/common';
import { TripStatus } from '@prisma/client';

describe('MatchService', () => {
  let service: MatchService;
  let prismaService: PrismaService;

  const mockPrismaService = {
    trip: {
      create: jest.fn(),
      findUnique: jest.fn(),
      findMany: jest.fn(),
      update: jest.fn(),
      updateMany: jest.fn(),
    },
    matchAttempt: {
      findUnique: jest.fn(),
      update: jest.fn(),
      updateMany: jest.fn(),
    },
    $transaction: jest.fn(async (callback) => {
      // Mocking the interactive transaction
      return callback(mockPrismaService);
    }),
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        MatchService,
        {
          provide: PrismaService,
          useValue: mockPrismaService,
        },
      ],
    }).compile();

    service = module.get<MatchService>(MatchService);
    prismaService = module.get<PrismaService>(PrismaService);
    
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  describe('acceptMatch', () => {
    const acceptDto = { tripId: 'trip-1', driverId: 'driver-1' };

    it('should successfully accept a match', async () => {
      const mockTrip = { id: 'trip-1', status: TripStatus.SEARCHING };
      
      mockPrismaService.trip.findUnique.mockResolvedValueOnce(mockTrip).mockResolvedValueOnce({ ...mockTrip, status: TripStatus.MATCHED, driverId: 'driver-1' });
      mockPrismaService.trip.updateMany.mockResolvedValueOnce({ count: 1 });
      mockPrismaService.matchAttempt.findUnique.mockResolvedValueOnce(null);

      const result = await service.acceptMatch(acceptDto);

      expect(result.status).toBe(TripStatus.MATCHED);
      expect(result.driverId).toBe('driver-1');
      expect(mockPrismaService.trip.updateMany).toHaveBeenCalledWith({
        where: { id: 'trip-1', status: TripStatus.SEARCHING },
        data: { status: TripStatus.MATCHED, driverId: 'driver-1' },
      });
    });

    it('should throw ConflictException if trip is no longer SEARCHING', async () => {
      const mockTrip = { id: 'trip-1', status: TripStatus.MATCHED };
      mockPrismaService.trip.findUnique.mockResolvedValueOnce(mockTrip);

      await expect(service.acceptMatch(acceptDto)).rejects.toThrow(ConflictException);
    });

    it('should throw ConflictException if concurrency updates return 0 count', async () => {
      const mockTrip = { id: 'trip-1', status: TripStatus.SEARCHING };
      mockPrismaService.trip.findUnique.mockResolvedValueOnce(mockTrip);
      mockPrismaService.trip.updateMany.mockResolvedValueOnce({ count: 0 }); // simulate race condition

      await expect(service.acceptMatch(acceptDto)).rejects.toThrow(ConflictException);
    });
  });

  describe('State Transitions', () => {
    it('arriveTrip should fail if not MATCHED', async () => {
      mockPrismaService.trip.findUnique.mockResolvedValueOnce({ id: 'trip-1', status: TripStatus.SEARCHING });
      await expect(service.arriveTrip('trip-1')).rejects.toThrow(BadRequestException);
    });

    it('startTrip should fail if not ARRIVED', async () => {
      mockPrismaService.trip.findUnique.mockResolvedValueOnce({ id: 'trip-1', status: TripStatus.MATCHED });
      await expect(service.startTrip('trip-1')).rejects.toThrow(BadRequestException);
    });

    it('completeTrip should fail if not IN_PROGRESS', async () => {
      mockPrismaService.trip.findUnique.mockResolvedValueOnce({ id: 'trip-1', status: TripStatus.ARRIVED });
      await expect(service.completeTrip('trip-1')).rejects.toThrow(BadRequestException);
    });

    it('arriveTrip should succeed if MATCHED', async () => {
      mockPrismaService.trip.findUnique.mockResolvedValueOnce({ id: 'trip-1', status: TripStatus.MATCHED });
      mockPrismaService.trip.update.mockResolvedValueOnce({ id: 'trip-1', status: TripStatus.ARRIVED });
      const result = await service.arriveTrip('trip-1');
      expect(result.status).toBe(TripStatus.ARRIVED);
    });
  });
});
