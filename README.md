# [При добавлении нового приема по плану падает ошибка](https://www.notion.so/5ec22d6ea2b348ae9d57f4ac2274094a)

Bitrix24: https://kvokka.bitrix24.ru/workgroups/group/211/tasks/task/view/6003/

Created: Mar 26, 2021 11:01 AM

Аналитик: Максим Викторович

Вид входящего: Проблемы

Подпроект: Админка 🤓

Приоритет: P1 🔥

Разработчик: Anton A

Ревьювер: Максим Викторович

Ссылка на ветку: http://git.kvokka.com/mlobanov/ishemia/-/tree/FIX-planAppointmentNew

Статус: To Review

Тестер: Igor' Nikiforov

Тип: Task 🔨

# Описание задачи

### Проблема

При добавлении нового приема по плану падает ошибка
URL: сейчас - /admin/plan_appointment/new

![https://s3.us-west-2.amazonaws.com/secure.notion-static.com/26689587-e261-42ea-be56-55a8dff9dce8/14.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20210330%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20210330T122508Z&X-Amz-Expires=86400&X-Amz-Signature=7e12de026c21dcf234b63b26f6464bb04b51cd5143bcb193f40e6947099cf704&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%2214.png%22](https://s3.us-west-2.amazonaws.com/secure.notion-static.com/26689587-e261-42ea-be56-55a8dff9dce8/14.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20210330%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20210330T122508Z&X-Amz-Expires=86400&X-Amz-Signature=7e12de026c21dcf234b63b26f6464bb04b51cd5143bcb193f40e6947099cf704&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%2214.png%22)

### Входные данные

1. Имеются кнопки `Обследование`, `Прием пациента` на странице просмотра истории болезни;
2. Имеются URL для добавления `Обследования`, `Приема пациента` со страницы просмотра истории болезни;
3. Присутствует условие при котором кнопка `Добавить назначение` не отображается;
4. Присутствует ошибка при добавлении `Обследования` при клике на кнопку `Добавить назначение` на обследование со страницы просмотра назначения;
5. Присутствует ошибка при добавлении `Приема пациента` при клике на кнопку `Добавить назначение на прием` со старинцы просмотра назначения.

### Выходные данные

Описание результата.
