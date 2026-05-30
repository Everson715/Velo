import { IsNotEmpty, IsUUID } from 'class-validator';

export class AcceptMatchDto {
  @IsUUID()
  @IsNotEmpty()
  tripId: string;

  @IsUUID()
  @IsNotEmpty()
  driverId: string;
}
