const day = { day: 'numeric', month: 'long' } as const;
const time = { hour: '2-digit', minute: '2-digit' } as const;

export function formatWhen(iso: string, timeZone: string): string {
    return new Intl.DateTimeFormat('ru-RU', { timeZone, ...day, ...time }).format(new Date(iso));
}

export function formatDay(iso: string, timeZone: string): string {
    return new Intl.DateTimeFormat('ru-RU', {
        timeZone,
        day: 'numeric',
        month: 'short',
        weekday: 'short',
    }).format(new Date(iso));
}

export function formatTime(iso: string, timeZone: string): string {
    return new Intl.DateTimeFormat('ru-RU', { timeZone, ...time }).format(new Date(iso));
}

export function dayKey(iso: string, timeZone: string): string {
    return new Intl.DateTimeFormat('sv-SE', { timeZone }).format(new Date(iso));
}
