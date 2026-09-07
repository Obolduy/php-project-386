import "@typespec/http";

using Http;

@service(#{ title: "Запись на звонок" })
namespace CallBooking;

@doc("Заготовка разговора, которую заводит владелец.")
model MeetingType {
  id: int32;
  title: string;
  description: string;
  durationMinutes: int32;
}

@doc("Данные для создания Типа встречи.")
model MeetingTypeInput {
  title: string;
  description: string;
  durationMinutes: int32;
}

@doc("Отрезок рабочего времени, на который можно записаться.")
model Slot {
  startsAt: utcDateTime;
  endsAt: utcDateTime;
}

@doc("Свободные слоты Типа встречи вместе с таймзоной, в которой их показывать.")
model SlotList {
  timezone: string;
  slots: Slot[];
}

@doc("Данные для создания Записи.")
model BookingInput {
  meetingTypeId: int32;
  startsAt: utcDateTime;
  guestName: string;
  guestEmail: string;
}

@doc("Запись гостя на конкретное время.")
model Booking {
  id: int32;
  meetingTypeId: int32;
  startsAt: utcDateTime;
  endsAt: utcDateTime;
  guestName: string;
  guestEmail: string;
}

@doc("Слот заняли между показом и отправкой формы.")
@error
model SlotTakenError {
  code: "slot_taken";
  message: string;
}

@doc("Ресурс не найден.")
@error
model NotFoundError {
  code: "not_found";
  message: string;
}

@route("/api/meeting-types")
namespace MeetingTypes {
  @doc("Список Типов встречи.")
  @get
  op list(): MeetingType[];

  @doc("Свободные слоты Типа встречи в окне записи.")
  @route("/{id}/slots")
  @get
  op slots(@path id: int32, @query from: plainDate, @query to: plainDate):
    | SlotList
    | NotFoundError;
}

@route("/api/bookings")
namespace Bookings {
  @doc("Записаться на свободный слот.")
  @post
  op create(@body input: BookingInput):
    | {
        @statusCode statusCode: 201;
        @body booking: Booking;
      }
    | SlotTakenError
    | NotFoundError;
}

@route("/api/admin/meeting-types")
namespace AdminMeetingTypes {
  @doc("Создать Тип встречи.")
  @post
  op create(@body input: MeetingTypeInput): {
    @statusCode statusCode: 201;
    @body meetingType: MeetingType;
  };
}

@route("/api/admin/bookings")
namespace AdminBookings {
  @doc("Предстоящие Записи всех Типов встречи одним списком.")
  @get
  op list(): Booking[];
}
