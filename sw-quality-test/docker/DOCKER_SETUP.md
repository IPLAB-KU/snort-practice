# SW Quality Test Docker 구축 가이드

이 문서는 XAMPP 기반 PHP 쇼핑몰 프로젝트를 Ubuntu 환경에서 Docker Compose로 실행하기 위한 가이드입니다.

## 1. 기준 버전

현재 XAMPP 화면 기준 버전은 다음과 같습니다.

| 구성 요소 | 버전 |
| --- | --- |
| Web Server | Apache 2.4.58 |
| PHP | PHP 8.2.12 |
| PHP Extensions | mysqli, curl, mbstring |
| Database | MySQL 8.0.39 |
| DB Charset | utf8mb4 |
| phpMyAdmin | 5.2.1 |

Docker 구성에서는 아래 이미지를 사용합니다.

| 서비스 | 이미지 |
| --- | --- |
| app | `php:8.2.12-apache` |
| db | `mysql:8.0.39` |
| phpmyadmin | `phpmyadmin:5.2.1` |

## 2. 파일 구조

Docker 관련 파일은 프로젝트 루트의 `docker/` 디렉터리에 둡니다.

```text
docker/
  Dockerfile
  apache-vhost.conf
  docker-compose.yml
  php.ini
  DOCKER_SETUP.md
```

## 3. Docker 설치

### 3.1 Ubuntu 설치

Ubuntu에서 Docker Engine과 Compose를 간단히 설치하려면 다음 명령어를 사용합니다.

```bash
sudo apt update
sudo apt install -y docker.io docker-compose-v2
sudo systemctl enable --now docker
```

설치 확인:

```bash
docker --version
docker compose version
```

현재 사용자를 `docker` 그룹에 추가하면 매번 `sudo` 없이 Docker를 사용할 수 있습니다.

```bash
sudo usermod -aG docker $USER
newgrp docker
```

만약 `docker-compose-v2` 패키지를 찾을 수 없다고 나오면 아래 명령어로 구버전 Compose를 설치합니다.

```bash
sudo apt install -y docker-compose
docker-compose --version
```

이 경우 실행 명령어는 `docker compose` 대신 `docker-compose`를 사용합니다.

```bash
docker-compose up -d --build
```

### 3.2 Kali 설치

Kali Linux에서는 `docker`라는 이름의 다른 패키지가 있으므로 컨테이너 Docker는 `docker.io`로 설치해야 합니다.

```bash
sudo apt update
sudo apt install -y docker.io
sudo systemctl enable --now docker
```

설치 확인:

```bash
docker --version
```

`sudo` 없이 Docker를 사용하려면 현재 사용자를 `docker` 그룹에 추가합니다.

```bash
sudo usermod -aG docker $USER
newgrp docker
```

Kali에서 Compose가 필요하면 먼저 아래 명령어를 확인합니다.

```bash
docker compose version
```

`docker compose`가 없으면 구버전 Compose 패키지를 설치합니다.

```bash
sudo apt install -y docker-compose
docker-compose --version
```

이 경우 실행 명령어는 다음처럼 사용합니다.

```bash
docker-compose up -d --build
```

### 3.3 Docker Desktop 설치

Ubuntu에서 GUI로 컨테이너를 관리하고 싶다면 Docker Desktop을 사용할 수 있습니다.

Ubuntu에서는 Docker Desktop for Linux가 공식 지원됩니다.

```text
https://docs.docker.com/desktop/setup/install/linux/ubuntu/
```

Docker Desktop과 Docker Engine을 같이 설치한 경우 컨텍스트가 나뉠 수 있습니다.

```bash
docker context ls
docker context use default
docker context use desktop-linux
```

과제 VM, 서버, Kali 환경에서는 보통 Docker Desktop보다 Docker Engine 방식이 더 단순하고 가볍습니다.

## 4. DB 설정

Docker에서는 PHP 컨테이너와 MySQL 컨테이너가 분리되어 있으므로 DB 호스트를 `127.0.0.1`로 두면 연결되지 않습니다.

다음 두 파일은 환경변수를 우선 사용하도록 변경하는 것을 권장합니다.

- `includes/db_config.php`
- `admin/includes/db_config.php`

권장 코드:

```php
<?php
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: 3306);
define('DB_NAME', getenv('DB_NAME') ?: 'sw_quality_test_shop');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '1234');
?>
```

이렇게 하면 XAMPP 실행 시에는 기존 로컬 DB 설정을 사용하고, Docker 실행 시에는 `docker-compose.yml`의 환경변수를 사용합니다.

## 5. 실행

프로젝트 루트에서 `docker/` 디렉터리로 이동한 뒤 실행합니다.

```bash
cd docker
docker compose up -d --build
```

상태 확인:

```bash
docker compose ps
```

로그 확인:

```bash
docker compose logs -f
```

## 6. 접속 정보

| 서비스 | 주소 |
| --- | --- |
| 쇼핑몰 | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |
| MySQL Host 접속 | `127.0.0.1:3307` |
| MySQL 컨테이너 내부 접속 | `db:3306` |

phpMyAdmin 로그인 정보:

```text
Server: db
User: root
Password: 1234
Database: sw_quality_test_shop
```

## 7. DB 초기화

최초 실행 시 MySQL 컨테이너는 아래 파일을 자동 실행합니다.

```text
sql/DB_init.sql
```

이 자동 실행은 DB 볼륨이 처음 생성될 때 한 번만 수행됩니다. SQL을 수정한 뒤 다시 초기화하려면 볼륨을 삭제해야 합니다.

```bash
docker compose down -v
docker compose up -d --build
```

주의: `down -v`는 MySQL 데이터 볼륨을 삭제하므로 기존 DB 데이터가 모두 삭제됩니다.

## 8. 테스트 데이터

`sql/TestData.sql`을 자동 삽입하려면 파일 인코딩과 SQL 문법을 먼저 확인한 뒤 `docker-compose.yml`의 `db.volumes`에 다음 줄을 추가합니다.

```yaml
- ../sql/TestData.sql:/docker-entrypoint-initdb.d/02_TestData.sql:ro
```

## 9. 중지 및 재시작

컨테이너 중지:

```bash
docker compose down
```

컨테이너 재시작:

```bash
docker compose up -d
```

이미지까지 새로 빌드:

```bash
docker compose build --no-cache
docker compose up -d
```

## 10. 문제 해결

### DB 연결 실패

Docker 환경에서는 PHP에서 MySQL 접속 호스트가 `db`여야 합니다.

```text
DB_HOST=db
DB_PORT=3306
```

### 포트 충돌

기본 포트는 다음과 같습니다.

| 서비스 | 호스트 포트 | 컨테이너 포트 |
| --- | --- | --- |
| Apache | 8080 | 80 |
| MySQL | 3307 | 3306 |
| phpMyAdmin | 8081 | 80 |

포트가 이미 사용 중이면 `docker-compose.yml`에서 왼쪽 포트를 변경합니다.

```yaml
ports:
  - "18080:80"
```

### 업로드 파일 권한

파일 업로드가 실패하면 `attachment/`와 `product_img/` 디렉터리 권한을 확인합니다.

```bash
chmod -R 775 ../attachment ../product_img
```

그래도 실패하면 Apache 컨테이너 로그를 확인합니다.

```bash
docker compose logs -f app
```
