export interface pagination_interface {
  current_page: number,
  first_page_url: string,
  from: number,
  last_page: number,
  last_page_url: string,
  // links?: Array<{}>,
  next_page_url: string | null,
  // path?: string,
  per_page: number,
  prev_page_url: string | null,
  to: number,
  total: number,

  // tambahan bukan bawaan laravel
  next_url?: string | null
}

export function latest_url(data: pagination_interface | undefined | null, whenNull:string):string
{
  return (data && data.next_url) ? data.next_url : whenNull;
}

export function set_per_page(n:number, data: pagination_interface, callback:Function)
{
  data.per_page = n;
  const latest_url = new URL(data.next_url ? data.next_url : data.first_page_url);
  latest_url.searchParams.set("per_page", data.per_page.toString());
  data.next_url = latest_url.toString();
  callback();
}

export function prev(data: pagination_interface, callback:Function)
{
  const latest_url = new URL(data.prev_page_url);
  latest_url.searchParams.set("per_page", data.per_page.toString());
  data.next_url = latest_url.toString();
  callback();
}

export function next(data: pagination_interface, callback:Function)
{
  const latest_url = new URL(data.next_page_url);
  latest_url.searchParams.set("per_page", data.per_page.toString());
  data.next_url = latest_url.toString();
  callback();
}

export function goto(n:number, data: pagination_interface, callback:Function)
{
  if(n >= data.from && n <= data.to){
    const latest_url = data.next_url ? data.next_url : data.first_page_url;
    data.next_url = latest_url.replace(/page=[0-9]+/, `page=${n}`)
    callback();
  }
}