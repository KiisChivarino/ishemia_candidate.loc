# **Команды запуска:**

**Запуск контейнеров**
`make up`

**Остановить контейнеры**
`make down`

**Остановить ВСЕ контейнеры + удалить ВСЕ Докер сети**
`make rm`

**Загрузить фикстуры**(Dump + Force + Load)
`make fixtures`

**Собрать проект**(Composer install, npm install, yarn encore dev, dump, force)
`make build`

**Doctrine Validate**
`make db-validate`

**Doctrine Dump + Force**
`make db-update`

**Composer install**
`make composer-install`

**npm install**
`make npm-install`

**yarn encore dev**
`make yarn`

**Добавить домены проекта в host файл**
`make host`

------------------
# **Тестирование:**

**Обновление тестовой базы**
`make db-update-tests`

**Загрузка фикстур в тестовую базу**
`make fixtures-tests`

Для запуска тестов должна быть создана тестовая база с именем `ishemia_test`. Она создастся автоматически только в случае, если мы запускаем докер с пустой(Или отсутствующей) папкой `.docker/conf/postgres/db-data`. В случае отсутствия базы её можно создать через pgAdmin

---
# **Адреса:**

**Сайт** http://localhost:8080/

**pgAdmin** http://localhost:5050/

---
# **Доступы СУБД:**

**PgAdmin:** Mail: `postgres@pg.com` | Password: `postgres111111111`

---
# **Доступы к БД:**

**PG:** username: `ishemia` | bdPassword: `ishemia` | host: `ishemia_pgadmin4_1` | port `5432` | dbName `ishemia`

---

# **Установка Docker на Ubuntu:**

Устанавливаем зависимости: `sudo apt install apt-transport-https ca-certificates curl software-properties-common`

Добавляем ключ репозитория Докера: `curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -`

Добавляем репозиторий Докера: `sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu focal stable"`

Обновляем индексы пакетов: `sudo apt update`

Переключаемся на репозиторий Докера: `apt-cache policy docker-ce`

Устанавливаем Докер: `sudo apt install docker-ce`

Проверяем, что Докер включен: `sudo systemctl status docker`

Даём возможность запускать команды Докера без Sudo: `sudo usermod -aG docker ${USER}`

Активируем изменения в группах пользователей `su - ${USER}`

Перезагружаем ПК

Также нужно установить Docker-composer 

Для упрощения работы с make командами можно установить расширение makefile для phpstorm

---

# **Решение проблема:**

- Если не запускается контейнер с базой данных/не сохраняется база после перезапуска контейнеров, то необходимо выставить права 777 для папки ./.docker/conf/postgres/db-data
- Если при обращении к pgadmin идёт бесконечная загрузка/не сохраняются сервера, настройки pgadmin - нужно выставить права 777 для папки ./.docker/conf/postgres/pgadmin

-----
# **Расположение:**

**Путь к логам веб-сервера:** `./.docker/conf/nginx/logs`

**Путь к конфигам веб-сервера:** `./.docker/conf/nginx/default.conf`

**Путь к php.ini:** `./.docker/conf/php/php.ini`

---

# **Остальные команды:**

**Пересобрать докер контейнер:** `docker-compose up -d --no-deps --build`

**Пересобрать контейнер без использования кэша:**
`docker-compose build --no-cache`

**Список доступных контейнеров:**
`docker ps`

**Удалить контейнеры**
`docker-compose rm`

**Остановить ВСЕ докер контейнеры**
`docker stop $(docker ps -qa)`

**удалить ВСЕ докер контейнеры**
`docker rm $(docker ps -qa)`

**Перейти в консоль контейнера**
`docker-compose exec Имя контейнера bash`

**Удалить Все докер сети**
`docker network prune`