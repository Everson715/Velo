import { Test, TestingModule } from '@nestjs/testing';
import { INestApplication, ValidationPipe } from '@nestjs/common';
import * as request from 'supertest';
import { AppModule } from './../src/app.module';

describe('MatchController (e2e)', () => {
  let app: INestApplication;

  beforeAll(async () => {
    const moduleFixture: TestingModule = await Test.createTestingModule({
      imports: [AppModule],
    }).compile();

    app = moduleFixture.createNestApplication();
    app.useGlobalPipes(
      new ValidationPipe({
        whitelist: true,
        forbidNonWhitelisted: true,
      }),
    );
    await app.init();
  });

  afterAll(async () => {
    await app.close();
  });

  describe('/match/request (POST)', () => {
    it('should return 400 for invalid payload', () => {
      return request(app.getHttpServer())
        .post('/match/request')
        .send({
          // missing passengerId and others
          originLat: 'invalid-type-string',
        })
        .expect(400);
    });

    it('should return 201 for valid payload', () => {
      return request(app.getHttpServer())
        .post('/match/request')
        .send({
          passengerId: '123e4567-e89b-12d3-a456-426614174000',
          originLat: -23.55052,
          originLng: -46.633308,
          originAddress: 'Paulista Avenue',
          destinationLat: -23.55052,
          destinationLng: -46.633308,
          destinationAddress: 'Consolacao',
          price: 15.5,
          distanceKm: 5.2,
          durationMin: 15,
        })
        .expect(201);
    });
  });

  describe('/match/accept (POST)', () => {
    it('should return 400 for invalid uuid', () => {
      return request(app.getHttpServer())
        .post('/match/accept')
        .send({
          tripId: 'invalid-uuid',
          driverId: 'invalid-uuid',
        })
        .expect(400);
    });
  });
});
