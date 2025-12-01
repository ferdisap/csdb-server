import moment from 'moment';

export interface DateTimeInterface {
  format: string;
  toString():string;
}

export class DateTime implements DateTimeInterface {
  format = 'MMM Do YY'; // MMMM Do YYYY, h:mm:ss a
  time: string = '';

  constructor(time: string, format?: string) {
    this.time = time;
    if (format) {
      this.format = format;
    }
  }

  static instance(data: {id:string, format?:string}){
    return new DateTime(data.id, data.format);
  }

  static cast(time: string, format?:string): DateTime {
    return new DateTime(time, format);
  }

  toString(): string {
    return moment(this.time).format(this.format);
  }

  toJson(): string{
    return moment(this.time).format(this.format);
  }
}