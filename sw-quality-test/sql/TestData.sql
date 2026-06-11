SET NAMES utf8mb4;

USE `sw_quality_test_shop`;

-- ✅ 1. 관리자(Admins)
INSERT INTO admins (email, password, name, role, status)
VALUES
('admin@shop.com', 'admin1234', '관리자', 'super', 'active'),
('manager@shop.com', 'manager1234', '매니저', 'manager', 'active');

-- ✅ 2. 일반 사용자(Users)
INSERT INTO users (email, password, name, phone, address, points, status)
VALUES
('hong@example.com', 'hong1234', '홍길동', '010-1234-5678', '서울시 강남구 역삼동', 50000, 'active'),
('kim@example.com',  'kim1234',  '김영희', '010-2222-3333', '서울시 송파구 잠실동', 25000, 'active'),
('lee@example.com',  'lee1234',  '이철수', '010-5555-8888', '부산시 해운대구', 10000, 'blocked');

-- ✅ 3. 상품(Products)
INSERT INTO products (name, price, stock, image_url, description, is_active)
VALUES
('무선 마우스', 15000, 100, 'product_img/mouse.jpg', '인체공학 디자인의 무선 마우스', 1),
('기계식 키보드', 89000, 50, 'product_img/keyboard.jpg', '청축 기계식 키보드', 1),
('게이밍 헤드셋', 69000, 30, 'product_img/headset.jpg', '7.1채널 서라운드 게이밍 헤드셋', 1),
('USB-C 케이블', 5000, 200, 'product_img/usb_cable.jpg', '1m 길이의 고속충전 케이블', 1),
('모니터 스탠드', 29000, 70, 'product_img/monitor_stand.jpg', '듀얼 모니터용 조절식 스탠드', 1),
-- 추가 15개
('27인치 모니터', 229000, 40, 'product_img/monitor.jpg', 'FHD IPS 패널 27인치 모니터', 1),
('게이밍 마우스패드', 12000, 150, 'product_img/mousepad.jpg', '대형 장패드 마우스패드', 1),
('노트북 거치대', 35000, 60, 'product_img/notebook_stand.jpg', '알루미늄 노트북 스탠드', 1),
('외장 SSD 1TB', 159000, 25, 'product_img/ssd.jpg', '고속 전송 USB 3.2 외장 SSD', 1),
('웹캠', 49000, 80, 'product_img/webcam.jpg', 'Full HD 화상회의용 웹캠', 1),
('블루투스 스피커', 79000, 45, 'product_img/speaker.jpg', '방수 기능 블루투스 스피커', 1),
('무선 충전기', 25000, 110, 'product_img/charger.jpg', '고속 무선 충전 패드', 1),
('그래픽 타블렛', 129000, 20, 'product_img/graphic_tablet.jpg', '디지털 드로잉용 타블렛', 1),
('스마트 워치', 199000, 35, 'product_img/smart_watch.jpg', '심박수 측정 스마트 워치', 1),
('게이밍 의자', 259000, 15, 'product_img/gaming_chair.jpg', '인체공학 게이밍 체어', 1),
('CPU 쿨러', 45000, 75, 'product_img/cpu_cooler.jpg', '저소음 공랭 CPU 쿨러', 1),
('RGB 키보드', 99000, 55, 'product_img/rgb_keyboard.jpg', 'RGB 백라이트 게이밍 키보드', 1),
('듀얼 모니터 암', 89000, 30, 'product_img/dual_monitor_arm.jpg', '높이 조절 가능한 듀얼 모니터 암', 1),
('USB 허브', 19000, 140, 'product_img/usb_hub.jpg', '멀티포트 USB 3.0 허브', 1),
('게이밍 마이크', 75000, 25, 'product_img/gaming_mic.jpg', '스트리밍용 콘덴서 마이크', 1);

-- ✅ 4. 게시판(Board Posts)
INSERT INTO board_posts (title, content, author_user_id, author_name, views)
VALUES
('첫 번째 게시글', '안녕하세요! 첫 게시글입니다.', 1, '홍길동', 12),
('배송 문의드립니다', '배송은 얼마나 걸리나요?', 2, '김영희', 20),
('포인트로 결제 가능한가요?', '상품 구매 시 포인트로 전액 결제할 수 있나요?', 3, '이철수', 8);

-- ✅ 5. 댓글(Board Comments)
INSERT INTO board_comments (post_id, author_user_id, author_name, content)
VALUES
(1, 2, '김영희', '환영합니다!'),
(1, 3, '이철수', '반갑습니다.'),
(2, 1, '홍길동', '보통 1~2일 정도 소요됩니다.'),
(3, 2, '김영희', '저도 궁금했어요.');

-- ✅ 6. 장바구니(Cart)
INSERT INTO cart (user_id, product_id, quantity)
VALUES
(1, 1, 2), -- 홍길동: 무선 마우스 2개
(1, 2, 1), -- 홍길동: 키보드 1개
(2, 3, 1); -- 김영희: 헤드셋 1개

-- ✅ 7. 주문(Orders)
INSERT INTO orders (order_number, user_id, total_price, shipping_fee, paid_points, payment_method, status,
                    receiver_name, receiver_phone, receiver_address, delivery_request)
VALUES
('VS-2025-00001', 1, 119000, 3000, 10000, 'MIX', 'PAID',
 '홍길동', '010-1234-5678', '서울시 강남구 역삼동', '문 앞에 놔주세요'),
('VS-2025-00002', 2, 69000, 0, 0, 'CARD', 'SHIPPING',
 '김영희', '010-2222-3333', '서울시 송파구 잠실동', '부재 시 경비실에 맡겨주세요');

-- ✅ 8. 주문 아이템(Order Items)
INSERT INTO order_items (order_id, product_id, product_name, option_info, quantity, price)
VALUES
(1, 1, '무선 마우스', NULL, 2, 15000),
(1, 2, '기계식 키보드', NULL, 1, 89000),
(2, 3, '게이밍 헤드셋', NULL, 1, 69000);

-- ✅ 9. 포인트 이력(Point History)
INSERT INTO point_history (user_id, type, delta, balance_after, ref_type, ref_id, memo)
VALUES
(1, 'EARN', 1000, 51000, 'ORDER', 1, '주문 적립'),
(1, 'SPEND', -10000, 41000, 'ORDER', 1, '포인트 결제'),
(2, 'EARN', 500, 25500, 'ORDER', 2, '결제 완료 적립');

-- ✅ 10. 감사 로그(Audit Logs)
INSERT INTO audit_logs (actor_type, actor_id, action, detail, ip, ua)
VALUES
('USER', 1, 'LOGIN', '사용자 로그인 성공', '192.168.0.5', 'Chrome'),
('ADMIN', 1, 'CREATE_PRODUCT', '상품 “무선 마우스” 등록', '127.0.0.1', 'Firefox'),
('USER', 2, 'PLACE_ORDER', 'VS-2025-00002 주문 완료', '192.168.0.8', 'Edge');

SELECT '✅ 평문 비밀번호 포함 테스트 데이터 삽입 완료!' AS result;