# [Ошибка при добавлении назначения на обследование](https://www.notion.so/400843d3a2ce4e40a5495a39a3a569bf?v=a3388d3507c64b088f0b1b2c107f4dcb&p=1ed0b82481d243d886e7d5ea1e1dce2c)

Created: Mar 26, 2021 10:14 AM

Status: In Dev

Type: Bug 🐞

Аналитик: Максим Викторович

Исполнитель: Anton A

Разработчик: Anton A

Ревьювер: Максим Викторович

Ссылка на ветку: http://git.kvokka.com/mlobanov/ishemia/-/tree/FIX-DropsWhenCreatingCaseHistory

Тестер: Igor' Nikiforov

Исполнитель: Anton A

Подпроект: Админка 🤓

Приоритет: P1 🔥

Разработчик: Anton A

Кнопки создания записи в историю болезни на странице "Записи в историю болезни" вообще не должно быть, записи должны создаваться только через страницу просмотра конкретной истории болезни. Ошибка появлялась из-за того, что скрипт не находил нужный ему ГЕТ параметр(Который не передавался после клика по кнопке).

Ссылка на ветку: http://git.kvokka.com/mlobanov/ishemia/-/tree/FIX-errorAddedPrescriptionTesting

![https://s3.us-west-2.amazonaws.com/secure.notion-static.com/071def5b-2117-4e71-994a-ff425e7e0a9f/3.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20210326%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20210326T085756Z&X-Amz-Expires=86400&X-Amz-Signature=a21ed35914d62039a731801095a8972fae366905265a9135c43a6c4debe23b28&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%223.png%22](https://s3.us-west-2.amazonaws.com/secure.notion-static.com/071def5b-2117-4e71-994a-ff425e7e0a9f/3.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20210326%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20210326T085756Z&X-Amz-Expires=86400&X-Amz-Signature=a21ed35914d62039a731801095a8972fae366905265a9135c43a6c4debe23b28&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%223.png%22)

Тестер: Igor' Nikiforov

На странице "Записи в истории болезни" отсутствует кнопка "Новая запись". Записи в историю болезни идут через страницу конкретной истории болезни

![https://s3.us-west-2.amazonaws.com/secure.notion-static.com/d61c76de-9950-46de-89f0-5c240a9e19cd/Untitled.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20210326%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20210326T085829Z&X-Amz-Expires=86400&X-Amz-Signature=bb7e6b1d14ac4558f4b3d89e5b17246273b2826c0380ba98339fb70897e17edb&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%22Untitled.png%22](https://s3.us-west-2.amazonaws.com/secure.notion-static.com/d61c76de-9950-46de-89f0-5c240a9e19cd/Untitled.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20210326%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20210326T085829Z&X-Amz-Expires=86400&X-Amz-Signature=bb7e6b1d14ac4558f4b3d89e5b17246273b2826c0380ba98339fb70897e17edb&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%22Untitled.png%22)

URL: admin/prescription_testing/new?prescription_id={id}

![https://s3.us-west-2.amazonaws.com/secure.notion-static.com/1aa4188e-0b56-4f14-8d12-a2b0e1fddb6d/8.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20210326%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20210326T130649Z&X-Amz-Expires=86400&X-Amz-Signature=182c7d78994ff9cbdbf6e742c45dabc5c033dcc45b7ba5e6dd19a8b9dfe54d2f&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%228.png%22](https://s3.us-west-2.amazonaws.com/secure.notion-static.com/1aa4188e-0b56-4f14-8d12-a2b0e1fddb6d/8.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20210326%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20210326T130649Z&X-Amz-Expires=86400&X-Amz-Signature=182c7d78994ff9cbdbf6e742c45dabc5c033dcc45b7ba5e6dd19a8b9dfe54d2f&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%228.png%22)
