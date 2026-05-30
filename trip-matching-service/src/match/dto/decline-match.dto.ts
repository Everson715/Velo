import { IsNotEmpty, IsUUID } from 'class-validator';

export class DeclineMatchDto {
  @IsUUID()
  @IsNotEmpty()
  tripId: string;

  @IsUUID()
  @IsNotEmpty()
  driverId: string;
}
