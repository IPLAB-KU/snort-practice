# 🛒 Vulnerable Shop  
의도적으로 취약한 구조를 포함한 연습용 쇼핑몰 프로젝트  
웹 보안 실습, 개인 공부, 수업/과제 용도

## 📘 프로젝트 개요

Vulnerable Shop은 웹 보안 실습을 위해 제작된 **의도적 취약점 기반 쇼핑몰**입니다.  
SQL Injection, XSS, CSRF, 파일 업로드 취약점, 인증/인가 취약점 등 다양한 보안 약점을 직접 구현하고 테스트할 수 있습니다.

프론트엔드부터 백엔드, DB 설계, 관리자 페이지, 포인트 기반 결제 시스템까지 직접 구축하며  
전체적인 웹 서비스 개발 + 보안 실습을 동시에 할 수 있도록 구성되어 있습니다.

## 🧱 기술 스택

### Backend
- PHP 8.x
- MySQL 8.x
- Apache(XAMPP for Windows)

### Frontend
- HTML / CSS / JavaScript
- TailwindCSS
- Simple UI 구조 기반

## 디렉터리 구조 및 파일 설명
```
vulnerable-shop/
├── admin/
│   ├── api/
│   │   ├── admin_login_process.php
│   │   ├── admin_logout_process.php
│   │   ├── manage_post_delete.php
│   │   ├── manage_product_delete.php
│   │   └── manage_user_delete.php
│   ├── includes/
│   │   ├── admin_session.php
│   │   ├── db_connect.php
│   │   ├── db_config.php
│   │   ├── footer.php
│   │   ├── head.php
│   │   ├── sidebar.php
│   │   └── pagination.php
│   ├── manage_product.php
│   ├── manage_product_create.php
│   ├── manage_product_edit.php
│   ├── manage_post_edit.php
│   ├── manage_board.php
│   ├── manage_post_edit.php
│   ├── manage_user.php
│   ├── log.php
│   ├── log_view.php
│   ├── login.php
│   └── index.php
│
├── api/
│   ├── cart_add.php
│   ├── cart_remove.php
│   ├── post_write_process.php
│   ├── post_edit_process.php
│   ├── post_delete_process.php
│   ├── comment_write_process.php
│   ├── comment_delete_process.php
│   ├── order_create.php
│   ├── login_process.php
│   ├── logout_process.php
│   └── register_process.php
│
├── includes/
│   ├── db_connect.php
│   ├── db_config.php
│   ├── head.php
│   ├── header.php
│   ├── footer.php
│   ├── session.php
│   ├── require_user.php
│   ├── require_login.php
│   ├── pagination.php
│   └── ...
│
├── product_img/
│   └── (uploaded images)
│
├── asset/
│   ├── css/
│   ├── js/
│   └── ...
│
├── index.php
├── login.php
├── register.php
├── products.php
├── product.php
├── cart.php
├── checkout.php
├── order_complete.php
├── mypage.php
├── mypage_profile.php
├── mypage_orders.php
├── mypage_order_view.php
├── board.php
├── post.php
├── post_write.php
├── post_edit.php
│
└── README.md (프로젝트 문서)
```

## 🧩 구현된 기능 요약

### ✔ 1. 회원 시스템
- 회원가입
- 로그인 / 로그아웃
- 세션 기반 인증
- 회원 정보 검증
- 포인트 보유 시스템
- `require_user.php` 로 보호되는 마이페이지 및 결제 시스템

### ✔ 2. 게시판 시스템
- 글 작성 / 수정 / 삭제
- 댓글 작성 / 삭제
- 사용자 검증 적용 (본인만 수정/삭제 가능)
- 관리자 페이지에서 게시글 관리 가능

### ✔ 3. 상품 관리
#### 사용자 기능
- 상품 목록 조회
- 상품 상세 페이지
- 장바구니 담기 / 삭제
- 바로구매 기능
- 포인트 기반 결제
- 주문 내역 조회
- 주문 상세 페이지 확인

#### 관리자 기능
- 상품 등록 / 수정 / 삭제
- 상품 이미지 업로드 (product_img/)
- 이미지 유효성 검사 및 저장 경로 설정 완료

### ✔ 4. 장바구니 / 결제
- 장바구니 추가 (`cart_add.php`)
- 장바구니 상품 제거 (`cart_remove.php`)
- 주문 생성 (`order_create.php`)
- 포인트 결제 처리
- 장바구니 자동 비우기
- 주문 완료 페이지 이동

### ✔ 5. 주문 기능
- `orders`, `order_items` 테이블 기반 저장
- 주문 상세 페이지 데이터 불러오기
- 배송지 정보 입력
- 결제 실패/성공 예외 처리


## 🛠 관리자(Admin) 페이지 기능

### 인증 기능
- `/admin/admin_login.php` 로그인 화면
- `admin_session.php` 로 관리자 인증 처리
- `admin_login_process.php` / `admin_logout_process.php`

### 관리 기능
- 회원 관리
- 상품 관리  
- 게시글 관리  
- 로그 페이지

### 사이드바 + 활성화 표시
- 현재 메뉴 강조
- 로그아웃 버튼 탑재됨

## 📌 최근 해결한 문제들 (Log)
- 상품 이미지 업로드 경로 설정 문제 (`__DIR__` 사용)
- Windows XAMPP에서 파일 퍼미션 문제 해결
- payment_method 컬럼 길이 부족하여 `"Data truncated"` 오류 발생 → 컬럼 수정
- 장바구니에서 상품 제거 처리 구현

## 🚀 앞으로 확장 가능 기능
- 관리자 로그 기록 기능 강화


