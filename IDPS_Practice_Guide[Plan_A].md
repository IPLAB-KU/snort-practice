# IDS/IPS Practice Guide (Plan A)

## Overview

이 문서는 대학 IDS/IPS 실습을 위한 **Plan A: 중앙 집중형 환경** 사용 가이드입니다.

실습자는 역할에 따라 다음 두 팀으로 나뉩니다.

- **Attack Team**: 웹 서버와 데이터베이스를 대상으로 공격 트래픽을 발생시킵니다.
- **Defense Team**: Ubuntu 서버에 SSH로 접속하여 Snort 탐지 상태, 룰, 알림 로그를 확인합니다.

이 문서는 강사가 사전에 준비한 실습 환경을 사용하는 학생용 가이드입니다.

## Environment Architecture

Plan A 환경은 하나의 Ubuntu 서버에 실습에 필요한 주요 구성 요소가 함께 배치된 구조입니다.

```text
Attack Team
    |
    |  공격 트래픽 발생
    v
+-----------------------------+
| Ubuntu Practice Server      |
|                             |
|  - Web Server               |
|  - Database                 |
|  - Snort IDS/IPS            |
|                             |
+-----------------------------+
    ^
    |  SSH 접속 및 모니터링
    |
Defense Team
```

Defense Team은 강사가 제공한 계정으로 서버에 접속한 뒤 Snort 상태, 룰 파일, 알림 로그를 확인합니다.

## SSH Connection

Defense Team은 터미널에서 다음 형식으로 Ubuntu 서버에 접속합니다.

```bash
ssh <USERNAME>@203.253.176.254 -p 8022
```

접속 시 비밀번호 입력 프롬프트가 나타나면 강사가 제공한 비밀번호를 입력합니다.

```text
password: <PASSWORD>
```

예시:

```bash
ssh user@203.253.176.254 -p 8022
```

접속 후 현재 사용자와 서버 정보를 확인합니다.

```bash
whoami
hostname
ip addr
```

SSH 접속을 종료하려면 다음 명령을 사용합니다.

```bash
exit
```

## Basic Linux Commands

실습 중 자주 사용하는 기본 Linux 명령입니다.

현재 위치 확인:

```bash
pwd
```

디렉터리 목록 확인:

```bash
ls
ls -al
```

디렉터리 이동:

```bash
cd /etc/snort
cd /var/log/snort
cd ~
```

파일 내용 확인:

```bash
cat filename
less filename
tail filename
```

최근 로그 실시간 확인:

```bash
tail -f filename
```

명령 실행 권한이 필요한 경우:

```bash
sudo command
```

현재 실행 중인 프로세스 확인:

```bash
ps aux
ps aux | grep snort
```

네트워크 연결 상태 확인:

```bash
ss -tulnp
```

시스템 로그 확인:

```bash
journalctl -xe
```

## Snort Monitoring

Snort가 실행 중인지 확인합니다.

```bash
sudo systemctl status snort
```

간단히 Snort 프로세스만 확인할 수도 있습니다.

```bash
ps aux | grep snort
```

Snort 서비스가 정상적으로 실행 중이면 `active (running)` 상태를 확인할 수 있습니다.

Snort 관련 시스템 로그를 확인합니다.

```bash
sudo journalctl -u snort
```

최근 로그만 확인하려면 다음 명령을 사용합니다.

```bash
sudo journalctl -u snort -n 50
```

실시간으로 Snort 서비스 로그를 확인합니다.

```bash
sudo journalctl -u snort -f
```

Snort 설정 파일 문법을 점검해야 할 경우 다음 명령을 사용할 수 있습니다.

```bash
sudo snort -T -c /etc/snort/snort.conf
```

## Snort Rule Management

Snort 룰 파일은 일반적으로 다음 위치에 있습니다.

```text
/etc/snort/rules/
```

로컬 실습용 룰 파일은 다음 위치를 사용합니다.

```text
/etc/snort/rules/local.rules
```

룰 파일 목록을 확인합니다.

```bash
ls -al /etc/snort/rules/
```

로컬 룰 파일 내용을 확인합니다.

```bash
sudo less /etc/snort/rules/local.rules
```

룰 파일을 수정합니다.

```bash
sudo nano /etc/snort/rules/local.rules
```

`nano` 에디터 기본 사용법은 다음과 같습니다.

```text
저장: Ctrl + O 입력 후 Enter
종료: Ctrl + X
저장하지 않고 종료: Ctrl + X 입력 후 N
현재 줄 잘라내기: Ctrl + K
붙여넣기: Ctrl + U
문자열 검색: Ctrl + W
```

수정 중 실수했거나 변경 내용을 저장하고 싶지 않다면 `Ctrl + X`를 누른 뒤 `N`을 입력합니다.

룰 예시:

```text
alert icmp any any -> any any (msg:"ICMP Packet Detected"; sid:1000001; rev:1;)
```

룰 수정 후 Snort 설정을 점검합니다.

```bash
sudo snort -T -c /etc/snort/snort.conf
```

점검 결과 문제가 없으면 Snort를 재시작합니다.

```bash
sudo systemctl restart snort
```

재시작 후 상태를 확인합니다.

```bash
sudo systemctl status snort
```

## Practice Workflow

권장 실습 흐름은 다음과 같습니다.

1. Defense Team은 SSH로 Ubuntu 서버에 접속합니다.

```bash
ssh <USERNAME>@203.253.176.254 -p 8022
```

2. Snort가 실행 중인지 확인합니다.

```bash
sudo systemctl status snort
```

3. 현재 적용된 로컬 룰을 확인합니다.

```bash
sudo less /etc/snort/rules/local.rules
```

4. Snort 알림 로그를 실시간으로 모니터링합니다.

```bash
sudo tail -f /var/log/snort/alert
```

5. Attack Team은 웹 서버 또는 데이터베이스를 대상으로 공격 트래픽을 발생시킵니다.

6. Defense Team은 Snort 알림 로그에서 탐지 여부를 확인합니다.

```bash
sudo tail -f /var/log/snort/alert
```

7. 필요한 경우 룰을 수정합니다.

```bash
sudo nano /etc/snort/rules/local.rules
```

8. 룰 문법을 점검합니다.

```bash
sudo snort -T -c /etc/snort/snort.conf
```

9. Snort를 재시작합니다.

```bash
sudo systemctl restart snort
```

10. 다시 공격 트래픽을 발생시키고 탐지 결과를 확인합니다.

```bash
sudo tail -f /var/log/snort/alert
```

### 동시성 제어를 고려한 실습 진행

Plan A는 여러 Defense Team 구성원이 하나의 중앙 서버를 함께 사용하는 방식입니다. 따라서 여러 명이 동시에 같은 룰 파일을 수정하거나 Snort를 동시에 재시작하면 다른 팀원의 작업이 덮어써지거나 탐지 결과가 혼동될 수 있습니다.

룰 수정이 필요한 경우 다음 순서를 권장합니다.

1. 룰을 수정하기 전에 현재 수정 담당자를 정합니다.

```text
현재 local.rules 수정 담당자: <USERNAME>
```

2. 다른 Defense Team 구성원은 수정 담당자가 작업 중일 때 같은 파일을 동시에 수정하지 않습니다.

```bash
sudo less /etc/snort/rules/local.rules
```

3. 수정 담당자는 변경 전 룰 파일을 백업합니다.

```bash
sudo cp /etc/snort/rules/local.rules /etc/snort/rules/local.rules.bak
```

4. 수정 담당자는 로컬 룰 파일을 수정합니다.

```bash
sudo nano /etc/snort/rules/local.rules
```

5. 룰을 추가할 때는 `sid` 값이 다른 팀원의 룰과 겹치지 않도록 확인합니다.

```bash
sudo grep "sid:" /etc/snort/rules/local.rules
```

룰 예시:

```text
alert icmp any any -> any any (msg:"Team Rule - ICMP Detected"; sid:1000101; rev:1;)
```

6. 저장 후 Snort 설정 문법을 점검합니다.

```bash
sudo snort -T -c /etc/snort/snort.conf
```

7. 문법 점검이 성공한 경우에만 Snort를 재시작합니다.

```bash
sudo systemctl restart snort
```

8. 재시작 후 상태를 확인합니다.

```bash
sudo systemctl status snort
```

9. 수정 담당자는 변경이 끝났음을 다른 팀원에게 알립니다.

```text
local.rules 수정 완료: <USERNAME>
```

10. 다른 Defense Team 구성원은 최신 룰 파일을 다시 확인한 뒤 모니터링을 계속합니다.

```bash
sudo less /etc/snort/rules/local.rules
sudo tail -f /var/log/snort/alert
```

동시 작업 시 권장 역할은 다음과 같습니다.

- **룰 수정 담당자**: `local.rules`를 수정하고 문법을 점검합니다.
- **로그 모니터링 담당자**: `/var/log/snort/alert`를 실시간으로 확인합니다.
- **재시작 담당자**: 문법 점검 성공 후 Snort를 재시작합니다.
- **공격 확인 담당자**: Attack Team의 공격 시간과 탐지 로그 발생 시간을 비교합니다.

한 명이 여러 역할을 맡을 수 있지만, 같은 시간에 여러 명이 `local.rules`를 동시에 수정하지 않는 것이 중요합니다.

## Troubleshooting

SSH 접속이 되지 않는 경우:

```bash
ssh <USERNAME>@<SERVER_IP>
```

확인 사항:

- 서버 IP 주소가 올바른지 확인합니다.
- 사용자 이름이 올바른지 확인합니다.
- 비밀번호 `<PASSWORD>`를 정확히 입력했는지 확인합니다.
- 같은 네트워크에 연결되어 있는지 확인합니다.

Snort 상태가 `active (running)`이 아닌 경우:

```bash
sudo systemctl status snort
sudo journalctl -u snort -n 50
```

룰 수정 후 Snort가 재시작되지 않는 경우:

```bash
sudo snort -T -c /etc/snort/snort.conf
```

문법 오류가 있는 룰을 수정한 뒤 다시 점검합니다.

알림 로그가 보이지 않는 경우:

```bash
ls -al /var/log/snort/
sudo tail -f /var/log/snort/alert
```

확인 사항:

- Attack Team이 실제로 트래픽을 발생시켰는지 확인합니다.
- 룰 조건이 공격 트래픽과 일치하는지 확인합니다.
- Snort 서비스가 실행 중인지 확인합니다.
- 로그 파일 경로가 실습 환경과 일치하는지 확인합니다.

권한 오류가 발생하는 경우:

```bash
sudo tail -f /var/log/snort/alert
sudo less /etc/snort/rules/local.rules
```

대부분의 Snort 설정 파일과 로그 파일은 일반 사용자 권한으로 읽을 수 없을 수 있으므로 `sudo`를 사용합니다.

## Notes

- 이 문서는 이미 준비된 중앙 집중형 실습 서버를 사용하는 학생용 가이드입니다.
- Defense Team은 강사가 제공한 SSH 계정만 사용합니다.
- 서버 구성 파일을 변경하기 전에는 반드시 강사의 지시에 따릅니다.
- 룰 수정 후에는 항상 문법 점검을 먼저 수행한 뒤 Snort를 재시작합니다.
- 여러 학생이 동시에 접속할 수 있으므로 `local.rules` 수정 담당자를 정한 뒤 작업합니다.
- Snort 재시작은 문법 점검이 성공한 뒤 한 명이 대표로 수행합니다.
- 실습이 끝나면 SSH 세션을 종료합니다.

```bash
exit
```
