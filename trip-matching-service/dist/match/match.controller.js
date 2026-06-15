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
var __param = (this && this.__param) || function (paramIndex, decorator) {
    return function (target, key) { decorator(target, key, paramIndex); }
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.MatchController = void 0;
const common_1 = require("@nestjs/common");
const match_service_1 = require("./match.service");
const create_match_request_dto_1 = require("./dto/create-match-request.dto");
const accept_match_dto_1 = require("./dto/accept-match.dto");
const decline_match_dto_1 = require("./dto/decline-match.dto");
let MatchController = class MatchController {
    matchService;
    constructor(matchService) {
        this.matchService = matchService;
    }
    async requestTrip(createMatchRequestDto) {
        return this.matchService.requestTrip(createMatchRequestDto);
    }
    async getTripStatus(tripId) {
        return this.matchService.getTripStatus(tripId);
    }
    async cancelTrip(tripId) {
        return this.matchService.cancelTrip(tripId);
    }
    async getAvailableTrips() {
        return this.matchService.getAvailableTrips();
    }
    async acceptMatch(acceptMatchDto) {
        return this.matchService.acceptMatch(acceptMatchDto);
    }
    async declineMatch(declineMatchDto) {
        return this.matchService.declineMatch(declineMatchDto);
    }
    async arriveTrip(tripId) {
        return this.matchService.arriveTrip(tripId);
    }
    async startTrip(tripId) {
        return this.matchService.startTrip(tripId);
    }
    async completeTrip(tripId) {
        return this.matchService.completeTrip(tripId);
    }
};
exports.MatchController = MatchController;
__decorate([
    (0, common_1.Post)('request'),
    __param(0, (0, common_1.Body)()),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [create_match_request_dto_1.CreateMatchRequestDto]),
    __metadata("design:returntype", Promise)
], MatchController.prototype, "requestTrip", null);
__decorate([
    (0, common_1.Get)('status/:tripId'),
    __param(0, (0, common_1.Param)('tripId')),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [String]),
    __metadata("design:returntype", Promise)
], MatchController.prototype, "getTripStatus", null);
__decorate([
    (0, common_1.Delete)('cancel/:tripId'),
    __param(0, (0, common_1.Param)('tripId')),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [String]),
    __metadata("design:returntype", Promise)
], MatchController.prototype, "cancelTrip", null);
__decorate([
    (0, common_1.Get)('available-trips'),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", []),
    __metadata("design:returntype", Promise)
], MatchController.prototype, "getAvailableTrips", null);
__decorate([
    (0, common_1.Post)('accept'),
    __param(0, (0, common_1.Body)()),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [accept_match_dto_1.AcceptMatchDto]),
    __metadata("design:returntype", Promise)
], MatchController.prototype, "acceptMatch", null);
__decorate([
    (0, common_1.Post)('decline'),
    __param(0, (0, common_1.Body)()),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [decline_match_dto_1.DeclineMatchDto]),
    __metadata("design:returntype", Promise)
], MatchController.prototype, "declineMatch", null);
__decorate([
    (0, common_1.Patch)('trip/:tripId/arrive'),
    __param(0, (0, common_1.Param)('tripId')),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [String]),
    __metadata("design:returntype", Promise)
], MatchController.prototype, "arriveTrip", null);
__decorate([
    (0, common_1.Patch)('trip/:tripId/start'),
    __param(0, (0, common_1.Param)('tripId')),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [String]),
    __metadata("design:returntype", Promise)
], MatchController.prototype, "startTrip", null);
__decorate([
    (0, common_1.Patch)('trip/:tripId/complete'),
    __param(0, (0, common_1.Param)('tripId')),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [String]),
    __metadata("design:returntype", Promise)
], MatchController.prototype, "completeTrip", null);
exports.MatchController = MatchController = __decorate([
    (0, common_1.Controller)('match'),
    __metadata("design:paramtypes", [match_service_1.MatchService])
], MatchController);
//# sourceMappingURL=match.controller.js.map