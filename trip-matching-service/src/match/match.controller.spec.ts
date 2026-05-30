import { Test, TestingModule } from '@nestjs/testing';
import { MatchController } from './match.controller';
import { MatchService } from './match.service';

// Criamos um mock simples para os métodos do service
const mockMatchService = () => ({
  createMatchRequest: jest.fn(),
  acceptMatch: jest.fn(),
  declineMatch: jest.fn(),
  getMatchStatus: jest.fn(),
  cancelMatch: jest.fn(),
  getAvailableTrips: jest.fn(),
  arriveTrip: jest.fn(),
  startTrip: jest.fn(),
  completeTrip: jest.fn(),
});

describe('MatchController', () => {
  let controller: MatchController;
  let service: MatchService;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [MatchController],
      providers: [
        {
          provide: MatchService,
          useFactory: mockMatchService, // Injeta o mock em vez do service real
        },
      ],
    }).compile();

    controller = module.get<MatchController>(MatchController);
    service = module.get<MatchService>(MatchService);
  });

  it('should be defined', () => {
    expect(controller).toBeDefined();
  });
});