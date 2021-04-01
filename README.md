# [Не выводится список пацинтов в кабинете врача при city = null у пациента](https://www.notion.so/city-null-e07b8eb406b5400993bf80fcc8d5a499)

Created: Mar 30, 2021 5:20 PM

Аналитик: Anton A

Время последнего изменения: Mar 31, 2021 2:33 PM

Исполнитель: Anton A

Отредактировано: Anton A

Подпроект: Кабинет врача 👨‍⚕️

Приоритет: P0 ⭐

Разработчик: Anton A

Ревьювер: Максим Викторович

Ссылка на ветку: http://git.kvokka.com/mlobanov/ishemia/-/tree/FIX-patientListCityErrorInDoctorOffice

Статус: In Dev

Тестер: Всеволод Лапшин

Тип: Bug 🐞

# Описание задачи

### Проблема

Если хоть у одного пациента на странице не будет заполнено поле city - будет ошибка

URL: [/doctor_office/patients](http://www.test1.loc/doctor_office/patients)

![https://s3.us-west-2.amazonaws.com/secure.notion-static.com/8818570d-b4d6-4bf3-b905-183993a851e0/Untitled.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20210331%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20210331T113443Z&X-Amz-Expires=86400&X-Amz-Signature=6935d842a8f6583f7960acb449392e81c1824f4001456708949d9b091b905aab&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%22Untitled.png%22](https://s3.us-west-2.amazonaws.com/secure.notion-static.com/8818570d-b4d6-4bf3-b905-183993a851e0/Untitled.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20210331%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20210331T113443Z&X-Amz-Expires=86400&X-Amz-Signature=6935d842a8f6583f7960acb449392e81c1824f4001456708949d9b091b905aab&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%22Untitled.png%22)
