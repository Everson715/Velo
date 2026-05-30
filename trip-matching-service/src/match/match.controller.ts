import {
  Body,
  Controller,
  Delete,
  Get,
  Param,
  Patch,
  Post,
} from '@nestjs/common';
import { MatchService } from './match.service';
import { CreateMatchRequestDto } from './dto/create-match-request.dto';
import { AcceptMatchDto } from './dto/accept-match.dto';
import { DeclineMatchDto } from './dto/decline-match.dto';

@Controller('match')
export class MatchController {
  constructor(private readonly matchService: MatchService) {}

  @Post('request')
  async requestTrip(@Body() createMatchRequestDto: CreateMatchRequestDto) {
    return this.matchService.requestTrip(createMatchRequestDto);
  }

  @Get('status/:tripId')
  async getTripStatus(@Param('tripId') tripId: string) {
    return this.matchService.getTripStatus(tripId);
  }

  @Delete('cancel/:tripId')
  async cancelTrip(@Param('tripId') tripId: string) {
    return this.matchService.cancelTrip(tripId);
  }

  @Get('available-trips')
  async getAvailableTrips() {
    return this.matchService.getAvailableTrips();
  }

  @Post('accept')
  async acceptMatch(@Body() acceptMatchDto: AcceptMatchDto) {
    return this.matchService.acceptMatch(acceptMatchDto);
  }

  @Post('decline')
  async declineMatch(@Body() declineMatchDto: DeclineMatchDto) {
    return this.matchService.declineMatch(declineMatchDto);
  }

  @Patch('trip/:tripId/arrive')
  async arriveTrip(@Param('tripId') tripId: string) {
    return this.matchService.arriveTrip(tripId);
  }

  @Patch('trip/:tripId/start')
  async startTrip(@Param('tripId') tripId: string) {
    return this.matchService.startTrip(tripId);
  }

  @Patch('trip/:tripId/complete')
  async completeTrip(@Param('tripId') tripId: string) {
    return this.matchService.completeTrip(tripId);
  }
}
