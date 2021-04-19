# [Ошибки на странице Редактирования/Добавления пациента](https://www.notion.so/de927b4583f140b6b834dc634eeb08e8)

Created: Apr 12, 2021 4:54 PM

Аналитик: Igor' Nikiforov

Вид входящего: Проблемы

Время последнего изменения: Apr 12, 2021 5:11 PM

Исполнитель: Anton A

Отредактировано: Anton A

Подпроект: Админка 🤓

Приоритет: P1 🔥

Разработчик: Anton A

Ревьювер: Максим Лобанов

Статус: In Dev

Тестер: Igor' Nikiforov

Тип: Bugfix 🐞

# Описание задачи

### Проблема

Нельзя сохранить пациента у которого отсутствует Отчество.
Так же падает ошибка, если заполнить только следующие поля:
1. Фамилия
2. Имя
3. Отчество
4. Телефон
5. Дата рождения
6. Адрес
7. Город
8. Больница
9. Дата возникновения инфаркта
10. Район
11. Текст клинического диагноза

![https://s3.us-west-2.amazonaws.com/secure.notion-static.com/0985d520-f1ea-4cde-90dd-ea4c82fcdddb/Untitled.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20210412%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20210412T142434Z&X-Amz-Expires=86400&X-Amz-Signature=43faa823ed9fc7359c132e63b4a269b2b6077419356940d81e83b652bd5d0e88&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%22Untitled.png%22](https://s3.us-west-2.amazonaws.com/secure.notion-static.com/0985d520-f1ea-4cde-90dd-ea4c82fcdddb/Untitled.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20210412%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20210412T142434Z&X-Amz-Expires=86400&X-Amz-Signature=43faa823ed9fc7359c132e63b4a269b2b6077419356940d81e83b652bd5d0e88&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%22Untitled.png%22)

URL's: `doctor_office/create_patient`, `doctor_office/patient/{patient}/medical_history/edit_personal_data`, `admin/patient/new`, `admin/patient/{patient}/edit`

### Входные данные

Описание функционала.

### Выходные данные

Описание результата.
