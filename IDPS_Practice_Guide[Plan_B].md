# IDS/IPS Practice Guide (Plan B)

## Overview

이 문서는 대학 IDS/IPS 실습을 위한 **Plan B: 분산형 실습 환경** 가이드입니다.

Plan B에서는 학생들이 **공격 수행원 1명과 수비 수행원 1명으로 2인 1조**를 구성하여 실습을 진행합니다. 수비 수행원은 VirtualBox에 Kali Linux 가상 머신을 준비하고, 강사가 제공한 Git 저장소와 Docker Compose 프로젝트를 이용하여 Web Server, Database, Snort를 실행합니다.

공격 수행원은 같은 조의 수비 수행원이 준비한 환경만 대상으로 공격을 수행합니다. 수비 수행원은 자신의 환경에서 Snort 로그와 알림을 확인하며 공격 트래픽이 탐지되는지 관찰합니다.

강사가 제공하는 자료는 다음과 같습니다.

- Kali Linux VirtualBox 이미지 또는 설치 가이드
- Git 저장소 주소
- Docker Compose 프로젝트

이 문서는 이미 제공된 설정 파일을 사용하여 실습 환경을 실행하고 확인하는 방법에 집중합니다.

## Environment Architecture

Plan B 환경은 2인 1조 단위로 독립적으로 구성됩니다.

```text
공격 수행원
    |
    |  같은 조 수비 수행원의 VM으로 공격 트래픽 전송
    v
+----------------------------------+
| 수비 수행원 VirtualBox VM        |
| Kali Linux                       |
|                                  |
|  Docker Compose Project          |
|  - Web Server                    |
|  - Database                      |
|  - Snort                         |
|                                  |
+----------------------------------+
    ^
    |
    |  수비 수행원
    |  모니터링 및 룰 확인
```

각 조의 수비 수행원은 자신의 VM IP 주소를 같은 조의 공격 수행원에게 전달해야 합니다.

```text
<VM_IP>
```

## VirtualBox Network Configuration

공격 수행원이 수비 수행원의 VM에 접근하려면 VirtualBox 네트워크 설정이 올바르게 구성되어야 합니다.

권장 설정은 **Bridged Adapter**입니다.

1. VirtualBox를 실행합니다.
2. 실습용 Kali Linux VM을 선택합니다.
3. `Settings` 메뉴를 엽니다.
4. `Network` 탭으로 이동합니다.
5. `Adapter 1`을 활성화합니다.
6. `Attached to` 항목을 `Bridged Adapter`로 설정합니다.
7. `Name` 항목에서 현재 사용 중인 실제 네트워크 어댑터를 선택합니다.
8. 설정을 저장한 뒤 VM을 시작합니다.

Kali Linux VM 안에서 IP 주소를 확인합니다.

```bash
ip addr
```

또는 다음 명령을 사용할 수 있습니다.

```bash
hostname -I
```

출력된 IP 주소 중 같은 실습 네트워크에서 접근 가능한 주소를 확인합니다.

```text
<VM_IP>
```

네트워크 연결을 확인합니다.

```bash
ping -c 4 8.8.8.8
```

DNS 동작을 확인합니다.

```bash
ping -c 4 google.com
```

같은 조의 공격 수행원 PC에서 VM 접근 여부를 확인할 수 있습니다.

```bash
ping <VM_IP>
```

## Kali Linux Setup

Kali Linux VM에 로그인한 뒤 기본 패키지 정보를 갱신합니다.

```bash
sudo apt update
```

기본 도구를 설치합니다.

```bash
sudo apt install -y curl git nano ca-certificates gnupg
```

`nano` 에디터가 설치되어 있는지 확인합니다.

```bash
nano --version
```

`nano` 에디터 기본 사용법은 다음과 같습니다.

```text
파일 열기: nano filename
저장: Ctrl + O 입력 후 Enter
종료: Ctrl + X
저장하지 않고 종료: Ctrl + X 입력 후 N
현재 줄 잘라내기: Ctrl + K
붙여넣기: Ctrl + U
문자열 검색: Ctrl + W
```

예시:

```bash
nano docker-compose.yml
```

수정 중 실수했거나 변경 내용을 저장하고 싶지 않다면 `Ctrl + X`를 누른 뒤 `N`을 입력합니다.

현재 사용자 정보를 확인합니다.

```bash
whoami
hostname
```

현재 IP 주소를 다시 확인합니다.

```bash
hostname -I
```

디스크 여유 공간을 확인합니다.

```bash
df -h
```

메모리 상태를 확인합니다.

```bash
free -h
```

## Git Clone

강사가 제공한 Git 저장소를 Kali Linux VM에 복제합니다.

```bash
git clone https://github.com/IPLAB-KU/snort-practice.git
```

복제된 프로젝트 디렉터리로 이동합니다.

```bash
cd snort-practice
```

프로젝트 파일을 확인합니다.

```bash
ls -al
```

Docker Compose 파일이 있는지 확인합니다.

```bash
ls -al docker-compose.yml
```

저장소 이름이 다르게 제공된 경우, `cd` 명령에서 실제 디렉터리 이름을 사용합니다.

```bash
cd <PROJECT_DIRECTORY>
```

## Docker Installation

Docker가 이미 설치되어 있는지 확인합니다.

```bash
docker --version
```

Docker Compose가 사용 가능한지 확인합니다.

```bash
docker compose version
```

Docker가 설치되어 있지 않은 경우 Kali Linux 패키지 저장소를 이용해 설치합니다.

필수 패키지를 설치합니다.

```bash
sudo apt update
sudo apt install -y docker.io docker-compose-plugin
```

설치 후 버전을 확인합니다.

```bash
docker --version
docker compose version
```

Docker 서비스를 시작하고 부팅 시 자동 실행되도록 설정합니다.

```bash
sudo systemctl enable --now docker
```

현재 사용자를 `docker` 그룹에 추가합니다.

```bash
sudo usermod -aG docker $USER
```

그룹 권한을 적용하려면 로그아웃 후 다시 로그인합니다.

```bash
exit
```

다시 로그인한 뒤 Docker 실행을 확인합니다.

```bash
docker --version
docker compose version
docker ps
```

`docker ps` 실행 시 권한 오류가 발생하면 VM에서 로그아웃 후 다시 로그인했는지 확인합니다.

만약 `docker compose version` 명령이 동작하지 않으면 Compose 패키지를 다시 확인합니다.

```bash
sudo apt update
sudo apt install -y docker-compose-plugin
docker compose version
```

## Environment Deployment

Git 저장소에서 Docker Compose 파일이 있는 프로젝트 디렉터리로 이동합니다.

```bash
cd snort-practice
```

현재 디렉터리의 파일을 확인합니다.

```bash
ls -al
```

Compose 파일을 확인합니다.

```bash
ls -al docker-compose.yml
```

실습 환경을 백그라운드로 실행합니다.

```bash
docker compose up -d
```

컨테이너 상태를 확인합니다.

```bash
docker compose ps
```

실행 로그를 확인합니다.

```bash
docker compose logs
```

특정 서비스 로그만 확인하려면 서비스 이름을 지정합니다.

```bash
docker compose logs web
docker compose logs db
docker compose logs snort
```

실습 환경을 중지할 때는 다음 명령을 사용합니다.

```bash
docker compose down
```

다시 시작하려면 다음 명령을 사용합니다.

```bash
docker compose up -d
```

## Environment Verification

컨테이너가 모두 실행 중인지 확인합니다.

```bash
docker compose ps
```

전체 Docker 컨테이너 목록을 확인합니다.

```bash
docker ps
```

Web Server가 응답하는지 VM 내부에서 확인합니다.

```bash
curl http://localhost
```

VM IP 주소를 이용해 확인합니다.

```bash
curl http://<VM_IP>
```

같은 조의 공격 수행원 PC에서 브라우저로 다음 주소에 접속할 수 있는지 확인합니다.

```text
http://<VM_IP>
```

열려 있는 포트를 확인합니다.

```bash
ss -tulnp
```

Docker 네트워크 상태를 확인합니다.

```bash
docker network ls
```

컨테이너 로그에서 오류가 있는지 확인합니다.

```bash
docker compose logs --tail=50
```

## Snort Monitoring

Snort 컨테이너가 실행 중인지 확인합니다.

```bash
docker compose ps snort
```

Snort 로그를 확인합니다.

```bash
docker compose logs snort
```

Snort 로그를 실시간으로 모니터링합니다.

```bash
docker compose logs -f snort
```

Snort 알림 로그 파일이 컨테이너 또는 볼륨에 연결되어 있는 경우, 강사가 안내한 경로에서 확인합니다.

일반적인 예시는 다음과 같습니다.

```bash
tail -f logs/snort/alert
```

권한이 필요한 경우 `sudo`를 사용합니다.

```bash
sudo tail -f logs/snort/alert
```

Snort 컨테이너 내부에서 로그를 확인해야 하는 경우 다음 명령을 사용할 수 있습니다.

```bash
docker compose exec snort sh
```

컨테이너 안에서 로그 디렉터리를 확인합니다.

```bash
ls -al /var/log/snort
```

컨테이너 셸을 종료합니다.

```bash
exit
```

Snort 서비스만 다시 시작합니다.

```bash
docker compose restart snort
```

다시 상태를 확인합니다.

```bash
docker compose ps snort
```

## Practice Workflow

권장 실습 흐름은 다음과 같습니다.

1. 공격 수행원 1명과 수비 수행원 1명으로 2인 1조를 구성합니다.

2. 수비 수행원은 Kali Linux VM을 실행합니다.

3. 수비 수행원은 VM의 IP 주소를 확인합니다.

```bash
hostname -I
```

4. 수비 수행원은 확인한 IP 주소를 같은 조의 공격 수행원에게 전달합니다.

```text
<VM_IP>
```

5. 수비 수행원은 Git 프로젝트 디렉터리로 이동합니다.

```bash
cd snort-practice
```

6. 수비 수행원은 실습 환경을 실행합니다.

```bash
docker compose up -d
```

7. 수비 수행원은 컨테이너 상태를 확인합니다.

```bash
docker compose ps
```

8. 수비 수행원은 Web Server 접속을 확인합니다.

```bash
curl http://<VM_IP>
```

9. 수비 수행원은 Snort 로그를 실시간으로 모니터링합니다.

```bash
docker compose logs -f snort
```

10. 공격 수행원은 같은 조 수비 수행원의 `<VM_IP>`를 대상으로 공격 트래픽을 발생시킵니다.

11. 수비 수행원은 Snort 로그 또는 알림 파일에서 탐지 결과를 확인합니다.

```bash
tail -f logs/snort/alert
```

12. 실습이 끝나면 수비 수행원은 환경을 중지합니다.

```bash
docker compose down
```

## Troubleshooting

VM에서 인터넷이 되지 않는 경우:

```bash
ping -c 4 8.8.8.8
ping -c 4 google.com
```

확인 사항:

- VirtualBox 네트워크가 `Bridged Adapter`로 설정되어 있는지 확인합니다.
- 올바른 실제 네트워크 어댑터가 선택되어 있는지 확인합니다.
- 학교 Wi-Fi 또는 실습실 네트워크에서 VM 통신이 허용되는지 확인합니다.

공격 수행원이 수비 수행원의 VM에 접근할 수 없는 경우:

```bash
hostname -I
ss -tulnp
```

확인 사항:

- 공격 수행원에게 전달한 `<VM_IP>`가 정확한지 확인합니다.
- 수비 수행원 VM과 공격 수행원 PC가 같은 네트워크에 있는지 확인합니다.
- Web Server 컨테이너가 실행 중인지 확인합니다.

```bash
docker compose ps
```

Docker 명령에서 권한 오류가 발생하는 경우:

```bash
sudo usermod -aG docker $USER
exit
```

다시 로그인한 뒤 확인합니다.

```bash
docker ps
```

컨테이너가 실행되지 않는 경우:

```bash
docker compose ps
docker compose logs --tail=100
```

특정 서비스 로그를 확인합니다.

```bash
docker compose logs web
docker compose logs db
docker compose logs snort
```

Web Server가 열리지 않는 경우:

```bash
curl http://localhost
curl http://<VM_IP>
docker compose ps
```

Snort 로그가 보이지 않는 경우:

```bash
docker compose ps snort
docker compose logs snort
docker compose logs -f snort
```

알림 파일 경로가 제공된 경우 해당 경로도 확인합니다.

```bash
ls -al logs/snort/
tail -f logs/snort/alert
```

환경을 처음부터 다시 실행해야 하는 경우:

```bash
docker compose down
docker compose up -d
docker compose ps
```

## Notes

- 이 문서는 공격 수행원 1명과 수비 수행원 1명이 2인 1조로 실습하는 분산형 환경을 안내합니다.
- 수비 수행원은 자신의 Kali Linux VM에 독립 실습 환경을 실행합니다.
- `<GIT_REPOSITORY>`와 `<VM_IP>`는 강사 안내에 따라 실제 값으로 바꾸어 사용합니다.
- Docker Compose 설정 파일은 강사가 제공한 것을 그대로 사용합니다.
- 수비 수행원은 자신의 VM IP 주소를 같은 조의 공격 수행원에게 전달합니다.
- 공격 수행원은 같은 조의 수비 수행원 환경만 공격해야 합니다.
- 로그 확인 터미널은 실습 중 계속 열어 두는 것이 좋습니다.
- 실습이 끝난 뒤에는 불필요한 컨테이너를 종료합니다.

```bash
docker compose down
```
