import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { catchError, map, Observable, throwError } from 'rxjs';
import { environment } from 'src/environments/environment';
import { FoodtrackDto } from '../models/foodtrack-dto';
import { ReservationDto } from '../models/reservation-dto';
import { ReservationSave } from '../models/reservation-save';
import { SocietyDto } from '../models/society-dto';
import { ReservationStoreService } from './reservation-store.service';

@Injectable({
  providedIn: 'root'
})
export class HttpService {

  constructor(private http: HttpClient, private reservationStoreService: ReservationStoreService) { }

  saveReservations(reservation: ReservationSave): Observable<any> {
    const headers = { 'content-type': 'application/json'}
    const body = JSON.stringify(reservation);
    return this.http.post(`${environment.url}/api/reservations`, body, {'headers':headers , observe: 'response'}).pipe(
      catchError(this.handleError)
    );
  }

  getLocations(): Observable<number[]> {
    return this.http.get<number[]>(`${environment.url}/api/locations`).pipe(
      catchError(this.handleError),
    );
  }

  setReservations(foodtrackID: number, societyId: number) {
    this.getReservations(foodtrackID, societyId).pipe(
      map((reservations: ReservationDto[]) => this.reservationStoreService.emitReservations(reservations)))
      .subscribe();
  }

  getReservations(foodtrackId: number, societyId: number): Observable<ReservationDto[]> {
    return this.http.get<ReservationDto[]>(`${environment.url}/api/reservations/${foodtrackId}/${societyId}`).pipe(
      catchError(this.handleError)
    );
  }

  getReservationsFromSociety(societyId: number, date: string): Observable<{locationNo: number}[]> {
    return this.http.get<{locationNo: number}[]>(`${environment.url}/api/reservations-society/${societyId}/${date}`).pipe(
      catchError(this.handleError)
    );
  }

  getSocieties(): Observable<SocietyDto[]> {

    return this.http.get<SocietyDto[]>(`${environment.url}/api/societies`).pipe(
      catchError(this.handleError)
    );
  }

  getFoodtracks(): Observable<FoodtrackDto[]> {
    return this.http.get<FoodtrackDto[]>(`${environment.url}/api/foodtracks`).pipe(
      catchError(this.handleError)
    );
  }

  handleError(error:any) {
    let errorMessage = '';
    if (error.error instanceof ErrorEvent) {
      // client-side error
      errorMessage = `Error: ${error.error.message}`;
    } else {
      // server-side error
      errorMessage = `Error Code: ${error.status}\nMessage: ${error.message}`;
    }
    console.log(errorMessage);
    return throwError(() => {
        return errorMessage;
    });
  }
}
