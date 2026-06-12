# IDS/IPS Practice

이 저장소는 대학 IDS/IPS 실습을 위한 안내 문서와 실습 자료를 포함합니다.

실습은 기본적으로 **Plan A: 중앙 집중형 환경**으로 진행합니다. Plan A에서는 강사가 준비한 하나의 서버에 Web Server, Database, Snort가 구성되어 있으며, 학생들은 SSH로 접속하여 Snort 로그 확인, 룰 수정, 탐지 결과 분석을 수행합니다.

만약 Plan A 환경 접속이 어렵거나 중앙 서버 사용에 문제가 발생하는 경우에는 **Plan B: 분산형 환경**으로 전환하여 진행합니다. Plan B에서는 공격 수행원 1명과 수비 수행원 1명이 2인 1조를 구성하고, 수비 수행원이 Kali Linux VM에서 Docker Compose 기반 실습 환경을 직접 실행합니다.

## Practice Plans

| Plan | 방식 | 사용 상황 | 가이드 |
|---|---|---|---|
| Plan A | 중앙 집중형 환경 | 기본 실습 방식 | [IDPS_Practice_Guide[Plan_A].md](./IDPS_Practice_Guide%5BPlan_A%5D.md) |
| Plan B | 분산형 환경 | Plan A 진행이 어려운 경우 | [IDPS_Practice_Guide[Plan_B].md](./IDPS_Practice_Guide%5BPlan_B%5D.md) |

## Plan A Summary

Plan A는 강사가 준비한 중앙 Ubuntu 서버를 사용하는 방식입니다.

- Attack Team은 중앙 서버의 Web Server 또는 Database를 대상으로 공격 트래픽을 발생시킵니다.
- Defense Team은 SSH로 중앙 서버에 접속합니다.
- Defense Team은 Snort 상태, 룰 파일, 알림 로그를 확인합니다.
- 여러 학생이 같은 서버를 사용하므로 룰 수정과 Snort 재시작 시 동시성 제어가 필요합니다.

자세한 내용은 [Plan A 가이드](./IDPS_Practice_Guide%5BPlan_A%5D.md)를 확인합니다.

## Plan B Summary

Plan B는 각 조가 독립된 실습 환경을 구성하는 방식입니다.

- 학생들은 공격 수행원 1명과 수비 수행원 1명으로 2인 1조를 구성합니다.
- 수비 수행원은 Kali Linux VM에서 Docker Compose 프로젝트를 실행합니다.
- 수비 수행원의 환경에는 Web Server, Database, Snort가 배포됩니다.
- 공격 수행원은 같은 조 수비 수행원의 VM만 대상으로 공격을 수행합니다.
- 수비 수행원은 Snort 로그와 알림 파일을 확인합니다.

자세한 내용은 [Plan B 가이드](./IDPS_Practice_Guide%5BPlan_B%5D.md)를 확인합니다.

## Recommended Flow

1. 먼저 [Plan A 가이드](./IDPS_Practice_Guide%5BPlan_A%5D.md)에 따라 중앙 서버 접속을 시도합니다.
2. Plan A 환경이 정상적으로 동작하면 Plan A로 실습을 진행합니다.
3. Plan A 접속 또는 운영에 문제가 있으면 강사 안내에 따라 [Plan B 가이드](./IDPS_Practice_Guide%5BPlan_B%5D.md)로 전환합니다.
4. Plan B에서는 각 조의 수비 수행원이 Kali Linux VM에서 Docker Compose 환경을 실행합니다.
5. 공격 수행원은 지정된 수비 수행원 환경만 대상으로 공격합니다.

## Notes

- 실습 중에는 강사가 지정한 Plan을 우선 따릅니다.
- Plan A와 Plan B를 동시에 섞어서 진행하지 않습니다.
- 공격 대상은 반드시 강사가 지정한 실습 환경으로 제한합니다.
- 모든 실습은 교육 목적으로만 수행합니다.
