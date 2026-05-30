import { IsNotEmpty, IsNumber, IsString, IsUUID, Min } from 'class-validator';

export class CreateMatchRequestDto {
  @IsUUID()
  @IsNotEmpty()
  passengerId: string;

  @IsNumber()
  @IsNotEmpty()
  originLat: number;

  @IsNumber()
  @IsNotEmpty()
  originLng: number;

  @IsString()
  @IsNotEmpty()
  originAddress: string;

  @IsNumber()
  @IsNotEmpty()
  destinationLat: number;

  @IsNumber()
  @IsNotEmpty()
  destinationLng: number;

  @IsString()
  @IsNotEmpty()
  destinationAddress: string;

  @IsNumber()
  @Min(0)
  price: number;

  @IsNumber()
  @Min(0)
  distanceKm: number;

  @IsNumber()
  @Min(0)
  durationMin: number;
}
