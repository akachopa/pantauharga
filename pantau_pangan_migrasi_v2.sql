-- PANTAU PANGAN v2 | Migrasi dari pantau_pangan_bengkulu_tengah_2026.sql versi awal
-- Jalankan SEKALI pada database v1, bukan database kosong. Backup + maintenance mode dahulu.
-- mysql --default-character-set=utf8mb4 nama_database < pantau_pangan_migrasi_v2.sql
-- Instalasi baru: impor file v1 dahulu, kemudian file ini. Jangan jalankan full v1 pada DB terisi.
-- Tidak menghapus tabel/laporan/harga/stok. Mengganti prosedur verifikasi dan template akses.
-- MySQL DDL melakukan implicit commit: jika gagal hentikan, jangan rerun buta; restore backup staging.
-- MySQL 8.0.16+/8.4; diperiksa statis, belum diuji pada server MySQL.
-- Semua contoh kebijakan/ambang bersifat SIMULASI, bukan kebijakan resmi Pemkab.
-- API wajib menerapkan fungsi akses pada setiap request web/Android, bukan sekadar hide menu.
-- Tidak ada SQL user untuk end-user; actor_id wajib berasal dari identitas sesi terautentikasi.
SET NAMES utf8mb4 COLLATE utf8mb4_0900_ai_ci;
SET time_zone='+07:00';

CREATE TABLE schema_migrations (
 version VARCHAR(60) PRIMARY KEY, applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 notes VARCHAR(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE app_menus (
 id BIGINT UNSIGNED PRIMARY KEY, code VARCHAR(60) NOT NULL UNIQUE,
 name VARCHAR(100) NOT NULL, sort_order INT NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE menu_permissions (
 menu_id BIGINT UNSIGNED NOT NULL, permission_id BIGINT UNSIGNED NOT NULL,
 PRIMARY KEY(menu_id,permission_id), FOREIGN KEY(menu_id) REFERENCES app_menus(id),
 FOREIGN KEY(permission_id) REFERENCES permissions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE user_permission_overrides (
 user_id BIGINT UNSIGNED NOT NULL, permission_id BIGINT UNSIGNED NOT NULL,
 effect ENUM('allow','deny') NOT NULL, changed_by BIGINT UNSIGNED NOT NULL,
 reason VARCHAR(500) NOT NULL, changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(user_id,permission_id), FOREIGN KEY(user_id) REFERENCES users(id),
 FOREIGN KEY(permission_id) REFERENCES permissions(id), FOREIGN KEY(changed_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE user_data_scopes (
 id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, user_id BIGINT UNSIGNED NOT NULL,
 permission_id BIGINT UNSIGNED NOT NULL, scope_type ENUM('organization','region','market','point') NOT NULL,
 organization_id BIGINT UNSIGNED, region_id BIGINT UNSIGNED, market_id BIGINT UNSIGNED, point_id BIGINT UNSIGNED,
 valid_from DATETIME NOT NULL, valid_until DATETIME, granted_by BIGINT UNSIGNED,
 revoked_by BIGINT UNSIGNED, revoked_at DATETIME, reason VARCHAR(500) NOT NULL,
 CHECK(valid_until IS NULL OR valid_until>valid_from),
 CHECK((scope_type='organization' AND organization_id IS NOT NULL AND region_id IS NULL AND market_id IS NULL AND point_id IS NULL)
 OR (scope_type='region' AND organization_id IS NULL AND region_id IS NOT NULL AND market_id IS NULL AND point_id IS NULL)
 OR (scope_type='market' AND organization_id IS NULL AND region_id IS NULL AND market_id IS NOT NULL AND point_id IS NULL)
 OR (scope_type='point' AND organization_id IS NULL AND region_id IS NULL AND market_id IS NULL AND point_id IS NOT NULL)),
 FOREIGN KEY(user_id) REFERENCES users(id), FOREIGN KEY(permission_id) REFERENCES permissions(id),
 FOREIGN KEY(organization_id) REFERENCES organizations(id), FOREIGN KEY(region_id) REFERENCES regions(id),
 FOREIGN KEY(market_id) REFERENCES markets(id), FOREIGN KEY(point_id) REFERENCES monitoring_points(id),
 FOREIGN KEY(granted_by) REFERENCES users(id), FOREIGN KEY(revoked_by) REFERENCES users(id),
 INDEX(user_id,permission_id,revoked_at,valid_from)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE partner_accounts (
 user_id BIGINT UNSIGNED NOT NULL, business_id BIGINT UNSIGNED NOT NULL,
 linked_by BIGINT UNSIGNED, linked_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(user_id,business_id), FOREIGN KEY(user_id) REFERENCES users(id),
 FOREIGN KEY(business_id) REFERENCES businesses(id), FOREIGN KEY(linked_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE access_change_logs (
 id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, actor_id BIGINT UNSIGNED,
 subject_user_id BIGINT UNSIGNED NOT NULL, change_type VARCHAR(60) NOT NULL,
 before_json JSON, after_json JSON NOT NULL, reason VARCHAR(500) NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(actor_id) REFERENCES users(id), FOREIGN KEY(subject_user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE users ADD COLUMN is_internal BOOLEAN NOT NULL DEFAULT FALSE;
UPDATE users u SET is_internal=TRUE WHERE EXISTS(
 SELECT 1 FROM user_roles ur JOIN roles r ON r.id=ur.role_id
 WHERE ur.user_id=u.id AND r.code IN ('operator','verifikator','pimpinan','admin'));
ALTER TABLE roles MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE permissions MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;
INSERT INTO roles(code,name) SELECT 'admin','Admin' WHERE NOT EXISTS(SELECT 1 FROM roles WHERE code='admin');
INSERT INTO permissions(code,description) VALUES
('dashboard.view','Buka dashboard sesuai cakupan'),('prices.view','Lihat tabulasi harga dan stok'),
('report.edit_draft','Ubah draft sendiri sesuai cakupan'),('report.revise','Usulkan revisi resmi oleh tim internal'),
('master.view','Lihat master data'),('master.manage','Kelola master data'),
('policy.view','Lihat paket kebijakan'),('policy.manage','Kelola draft rule kebijakan'),
('policy.publish','Publikasikan versi rule'),('policy.approve','Setujui kasus rekomendasi kebijakan'),
('action.view','Lihat tindak lanjut'),('reports.view','Buka menu laporan'),
('access.manage','Kelola template izin dan cakupan pengguna'),('audit.view','Lihat audit aktivitas');
INSERT INTO app_menus VALUES
(1,'dashboard','Dashboard',1),(2,'prices','Input Harga dan Tabulasi',2),
(3,'master','Master Data',3),(4,'policies','Paket Kebijakan',4),
(5,'actions','Tindak Lanjut',5),(6,'reports','Laporan',6),(7,'settings','Pengaturan',7);
INSERT INTO menu_permissions(menu_id,permission_id)
SELECT CASE
 WHEN code IN ('dashboard.view','analytics.read') THEN 1
 WHEN code IN ('prices.view','report.create','report.edit_draft','report.revise','report.verify','correction.review') THEN 2
 WHEN code IN ('master.view','master.manage','point.manage','supply.manage') THEN 3
 WHEN code LIKE 'policy.%' OR code='alerts.validate' THEN 4
 WHEN code LIKE 'action.%' THEN 5
 WHEN code IN ('reports.view','report.export') THEN 6 ELSE 7 END,id
FROM permissions WHERE code NOT IN ('public.read','correction.create');
-- Template tambahan menggunakan nama peran, tanpa bergantung pada ID seed.
INSERT INTO role_permissions(role_id,permission_id)
SELECT r.id,p.id FROM roles r CROSS JOIN permissions p WHERE
 (r.code='admin') OR
 (r.code='operator' AND p.code IN ('dashboard.view','prices.view','report.edit_draft','report.revise','master.view','master.manage','policy.view','action.view','reports.view')) OR
 (r.code='mitra' AND p.code IN ('dashboard.view','prices.view','report.edit_draft','master.view')) OR
 (r.code='verifikator' AND p.code IN ('dashboard.view','prices.view','master.view','policy.view','action.view','reports.view')) OR
 (r.code='pimpinan' AND p.code IN ('dashboard.view','prices.view','master.view','policy.view','policy.approve','action.view','reports.view'))
ON DUPLICATE KEY UPDATE permission_id=VALUES(permission_id);
-- Pengelolaan akun/konfigurasi global sekarang hanya admin; tidak otomatis tetap pada operator.
DELETE rp FROM role_permissions rp JOIN roles r ON r.id=rp.role_id JOIN permissions p ON p.id=rp.permission_id
 WHERE r.code<>'admin' AND p.code IN ('users.manage','settings.manage','access.manage');

-- hierarchy: descendants include self; prevent region cycles in admin service.
CREATE VIEW v_region_ancestors AS
WITH RECURSIVE tree AS (
 SELECT id descendant_id,id ancestor_id,parent_id,0 depth FROM regions
 UNION ALL SELECT t.descendant_id,r.id,r.parent_id,t.depth+1
 FROM tree t JOIN regions r ON r.id=t.parent_id WHERE t.depth<16
) SELECT descendant_id,ancestor_id FROM tree;
CREATE VIEW v_effective_user_permissions AS
SELECT DISTINCT u.id user_id,p.id permission_id,p.code FROM users u CROSS JOIN permissions p
WHERE u.is_active=TRUE AND NOT EXISTS(
 SELECT 1 FROM user_permission_overrides o WHERE o.user_id=u.id AND o.permission_id=p.id AND o.effect='deny')
AND (EXISTS(SELECT 1 FROM user_roles ur JOIN role_permissions rp ON rp.role_id=ur.role_id
 WHERE ur.user_id=u.id AND rp.permission_id=p.id) OR EXISTS(
 SELECT 1 FROM user_permission_overrides o WHERE o.user_id=u.id AND o.permission_id=p.id AND o.effect='allow'));
CREATE VIEW v_user_visible_menus AS
SELECT DISTINCT e.user_id,m.id menu_id,m.code,m.name,m.sort_order
FROM v_effective_user_permissions e JOIN menu_permissions mp ON mp.permission_id=e.permission_id JOIN app_menus m ON m.id=mp.menu_id;

DELIMITER $$
CREATE FUNCTION fn_has_permission(p_user BIGINT UNSIGNED,p_code VARCHAR(80)) RETURNS BOOLEAN
READS SQL DATA SQL SECURITY INVOKER
BEGIN
 RETURN EXISTS(SELECT 1 FROM v_effective_user_permissions WHERE user_id=p_user AND code=p_code);
END$$
CREATE FUNCTION fn_can_point(p_user BIGINT UNSIGNED,p_code VARCHAR(80),p_point BIGINT UNSIGNED) RETURNS BOOLEAN
READS SQL DATA SQL SECURITY INVOKER
BEGIN
 IF NOT fn_has_permission(p_user,p_code) THEN RETURN FALSE; END IF;
 RETURN EXISTS(SELECT 1 FROM user_data_scopes s JOIN permissions perm ON perm.id=s.permission_id
 JOIN users u ON u.id=s.user_id JOIN organizations org ON org.id=u.organization_id
 JOIN monitoring_points pt ON pt.id=p_point
 WHERE s.user_id=p_user AND perm.code=p_code AND s.revoked_at IS NULL
 AND s.valid_from<=CURRENT_TIMESTAMP AND (s.valid_until IS NULL OR s.valid_until>CURRENT_TIMESTAMP)
 AND EXISTS(SELECT 1 FROM v_region_ancestors WHERE descendant_id=pt.region_id AND ancestor_id=org.region_id)
 AND ((s.scope_type='organization' AND s.organization_id=u.organization_id)
 OR (s.scope_type='region' AND EXISTS(SELECT 1 FROM v_region_ancestors WHERE descendant_id=pt.region_id AND ancestor_id=s.region_id))
 OR (s.scope_type='market' AND s.market_id=pt.market_id)
 OR (s.scope_type='point' AND s.point_id=pt.id)));
END$$
CREATE TRIGGER bu_access_change_logs BEFORE UPDATE ON access_change_logs FOR EACH ROW
BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Access audit is append-only'; END$$
CREATE TRIGGER bd_access_change_logs BEFORE DELETE ON access_change_logs FOR EACH ROW
BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Access audit is append-only'; END$$
DELIMITER ;

-- Migrasi scope input: pertahankan penugasan sebelumnya, tidak memperluas pasar secara diam-diam.
INSERT INTO user_data_scopes(user_id,permission_id,scope_type,point_id,valid_from,valid_until,reason)
SELECT a.user_id,p.id,'point',a.point_id,TIMESTAMP(a.starts_on),
 IF(a.ends_on IS NULL,NULL,TIMESTAMP(a.ends_on)+INTERVAL 1 DAY),'Migrasi penugasan v1'
FROM point_assignments a CROSS JOIN permissions p WHERE p.code IN ('report.create','report.edit_draft','report.revise','prices.view','master.view','dashboard.view');
-- Data global untuk pembaca internal; tidak otomatis memberi input global.
INSERT INTO user_data_scopes(user_id,permission_id,scope_type,organization_id,valid_from,reason)
SELECT DISTINCT u.id,p.id,'organization',u.organization_id,'2026-01-01 00:00:00','Migrasi cakupan internal; tinjau oleh admin'
FROM users u JOIN user_roles ur ON ur.user_id=u.id JOIN roles r ON r.id=ur.role_id CROSS JOIN permissions p
WHERE r.code IN ('operator','verifikator','pimpinan') AND p.code IN
('dashboard.view','analytics.read','prices.view','master.view','policy.view','action.view','reports.view','report.export','report.verify','policy.approve','action.assign','action.update','correction.review','alerts.validate');
-- Baris scope tidak memberi permission: keduanya wajib terpenuhi.
INSERT INTO access_change_logs(subject_user_id,change_type,after_json,reason)
SELECT id,'v2_scope_migration',JSON_OBJECT('source','point_assignments and internal template','review_required',TRUE),
'Migrasi oleh DBA; review sebelum aplikasi dibuka kembali' FROM users;

CREATE TABLE policy_packages (
 id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, organization_id BIGINT UNSIGNED NOT NULL,
 code VARCHAR(80) NOT NULL UNIQUE, name VARCHAR(180) NOT NULL, description TEXT,
 created_by BIGINT UNSIGNED, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(organization_id) REFERENCES organizations(id), FOREIGN KEY(created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE policy_versions (
 id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, package_id BIGINT UNSIGNED NOT NULL, version_no INT NOT NULL,
 state ENUM('draft','published','retired') NOT NULL DEFAULT 'draft', severity ENUM('info','warning','critical') NOT NULL,
 valid_from DATETIME NOT NULL, valid_until DATETIME, cooldown_hours INT NOT NULL DEFAULT 24,
 recommendation TEXT NOT NULL, requires_approval BOOLEAN NOT NULL DEFAULT TRUE,
 published_by BIGINT UNSIGNED, published_at DATETIME, frozen_definition JSON,
 is_demo BOOLEAN NOT NULL DEFAULT TRUE, UNIQUE(package_id,version_no),
 CHECK(version_no>0 AND cooldown_hours>=0), CHECK(requires_approval=TRUE),
 CHECK(valid_until IS NULL OR valid_until>valid_from),
 CHECK(state='draft' OR (published_at IS NOT NULL AND frozen_definition IS NOT NULL)),
 FOREIGN KEY(package_id) REFERENCES policy_packages(id), FOREIGN KEY(published_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE policy_rule_groups (
 id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, version_id BIGINT UNSIGNED NOT NULL,
 parent_group_id BIGINT UNSIGNED, logic ENUM('AND','OR') NOT NULL, sort_order INT NOT NULL DEFAULT 0,
 UNIQUE(id,version_id),
 FOREIGN KEY(version_id) REFERENCES policy_versions(id),
 FOREIGN KEY(parent_group_id,version_id) REFERENCES policy_rule_groups(id,version_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE policy_rule_conditions (
 id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, group_id BIGINT UNSIGNED NOT NULL,
 metric ENUM('price_change_pct','stock_days','report_overdue_hours','shipment_delay_hours') NOT NULL,
 comparator ENUM('gt','gte','lt','lte','eq') NOT NULL, threshold DECIMAL(16,4) NOT NULL,
 baseline ENUM('previous_observation','days_ago','none') NOT NULL,
 lookback_days INT NOT NULL DEFAULT 0, max_age_hours INT NOT NULL DEFAULT 48,
 min_observations INT NOT NULL DEFAULT 1, price_type ENUM('retail','wholesale','producer') NOT NULL DEFAULT 'retail',
 aggregation ENUM('point_latest','point_average') NOT NULL DEFAULT 'point_latest',
 CHECK(lookback_days>=0 AND max_age_hours>0 AND min_observations>0),
 CHECK(metric<>'price_change_pct' OR (baseline<>'none' AND min_observations>=2)),
 FOREIGN KEY(group_id) REFERENCES policy_rule_groups(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE policy_targets (
 id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, version_id BIGINT UNSIGNED NOT NULL,
 region_id BIGINT UNSIGNED, market_id BIGINT UNSIGNED, point_id BIGINT UNSIGNED, commodity_id BIGINT UNSIGNED,
 CHECK((region_id IS NOT NULL)+(market_id IS NOT NULL)+(point_id IS NOT NULL)=1),
 FOREIGN KEY(version_id) REFERENCES policy_versions(id), FOREIGN KEY(region_id) REFERENCES regions(id),
 FOREIGN KEY(market_id) REFERENCES markets(id), FOREIGN KEY(point_id) REFERENCES monitoring_points(id),
 FOREIGN KEY(commodity_id) REFERENCES commodities(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE policy_evaluation_runs (
 id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, version_id BIGINT UNSIGNED NOT NULL,
 evaluated_at DATETIME NOT NULL, data_cutoff_at DATETIME NOT NULL, engine_version VARCHAR(60) NOT NULL,
 mode ENUM('test','live') NOT NULL DEFAULT 'test', status ENUM('running','completed','failed') NOT NULL,
 definition_snapshot JSON NOT NULL, error_message TEXT,
 FOREIGN KEY(version_id) REFERENCES policy_versions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE policy_evaluation_results (
 id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, run_id BIGINT UNSIGNED NOT NULL,
 point_id BIGINT UNSIGNED NOT NULL, commodity_id BIGINT UNSIGNED NOT NULL,
 outcome ENUM('matched','not_matched','insufficient_data') NOT NULL,
 evidence_snapshot JSON NOT NULL, is_demo BOOLEAN NOT NULL DEFAULT TRUE,
 UNIQUE(run_id,point_id,commodity_id), FOREIGN KEY(run_id) REFERENCES policy_evaluation_runs(id),
 FOREIGN KEY(point_id) REFERENCES monitoring_points(id), FOREIGN KEY(commodity_id) REFERENCES commodities(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE policy_cases (
 id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, result_id BIGINT UNSIGNED NOT NULL UNIQUE,
 dedupe_key VARCHAR(190) NOT NULL UNIQUE, status ENUM('pending','approved','rejected','in_progress','closed') NOT NULL DEFAULT 'pending',
 requested_by BIGINT UNSIGNED, decided_by BIGINT UNSIGNED, decided_at DATETIME, decision_note TEXT,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(result_id) REFERENCES policy_evaluation_results(id), FOREIGN KEY(requested_by) REFERENCES users(id),
 FOREIGN KEY(decided_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE policy_case_events (
 id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, case_id BIGINT UNSIGNED NOT NULL, actor_id BIGINT UNSIGNED,
 event_type VARCHAR(60) NOT NULL, note TEXT NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(case_id) REFERENCES policy_cases(id), FOREIGN KEY(actor_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE action_plans ADD COLUMN policy_case_id BIGINT UNSIGNED,
 ADD CONSTRAINT fk_actions_policy_case FOREIGN KEY(policy_case_id) REFERENCES policy_cases(id);
ALTER TABLE action_plans ADD COLUMN target_point_id BIGINT UNSIGNED,
 ADD CONSTRAINT fk_actions_target_point FOREIGN KEY(target_point_id) REFERENCES monitoring_points(id);
-- action_plans existing rows retained; NULL target must be backfilled by admin before scoped API access.

DELIMITER $$
CREATE PROCEDURE sp_decide_policy_case(IN p_case BIGINT UNSIGNED,IN p_actor BIGINT UNSIGNED,IN p_decision VARCHAR(20),IN p_note TEXT)
SQL SECURITY INVOKER
BEGIN
 DECLARE v_status VARCHAR(20); DECLARE v_point BIGINT UNSIGNED; DECLARE v_outcome VARCHAR(30);
 DECLARE v_mode VARCHAR(10); DECLARE v_demo BOOLEAN; DECLARE v_demo_user BOOLEAN; DECLARE v_requester BIGINT UNSIGNED;
 DECLARE v_version_state VARCHAR(20);
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 START TRANSACTION;
 SELECT c.status,e.point_id,e.outcome,r.mode,e.is_demo,c.requested_by,v.state INTO v_status,v_point,v_outcome,v_mode,v_demo,v_requester,v_version_state
 FROM policy_cases c JOIN policy_evaluation_results e ON e.id=c.result_id JOIN policy_evaluation_runs r ON r.id=e.run_id JOIN policy_versions v ON v.id=r.version_id
 WHERE c.id=p_case FOR UPDATE;
 IF v_status IS NULL OR v_status<>'pending' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Pending case required'; END IF;
 IF NOT fn_can_point(p_actor,'policy.approve',v_point) OR p_actor=v_requester OR NOT EXISTS(SELECT 1 FROM users WHERE id=p_actor AND is_internal=TRUE) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Approval scope denied or self approval'; END IF;
 IF p_decision IS NULL OR p_decision NOT IN ('approved','rejected') OR p_note IS NULL OR CHAR_LENGTH(TRIM(p_note))=0 THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Decision and note required'; END IF;
 IF p_decision='approved' AND (v_outcome<>'matched' OR v_mode<>'live' OR v_version_state<>'published') THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Only matched live evaluation can be approved'; END IF;
 SELECT is_demo INTO v_demo_user FROM users WHERE id=p_actor;
 IF v_demo<>v_demo_user THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Demo and production must remain separate'; END IF;
 UPDATE policy_cases SET status=p_decision,decided_by=p_actor,decided_at=CURRENT_TIMESTAMP,decision_note=p_note WHERE id=p_case;
 INSERT INTO policy_case_events(case_id,actor_id,event_type,note) VALUES(p_case,p_actor,p_decision,p_note);
 COMMIT;
END$$
CREATE TRIGGER bi_policy_action BEFORE INSERT ON action_plans FOR EACH ROW
BEGIN
 IF NEW.policy_case_id IS NOT NULL AND NOT EXISTS(
 SELECT 1 FROM policy_cases c JOIN policy_evaluation_results e ON e.id=c.result_id
 WHERE c.id=NEW.policy_case_id AND c.status IN ('approved','in_progress') AND e.is_demo=NEW.is_demo
 AND e.point_id=NEW.target_point_id) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Policy action requires approved case and matching target'; END IF;
END$$
CREATE TRIGGER bu_policy_action BEFORE UPDATE ON action_plans FOR EACH ROW
BEGIN
 IF NOT (NEW.policy_case_id<=>OLD.policy_case_id) OR NOT (NEW.target_point_id<=>OLD.target_point_id) OR NEW.is_demo<>OLD.is_demo THEN
 IF OLD.policy_case_id IS NOT NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Policy action linkage immutable'; END IF;
 IF NEW.policy_case_id IS NOT NULL AND NOT EXISTS(
 SELECT 1 FROM policy_cases c JOIN policy_evaluation_results e ON e.id=c.result_id
 WHERE c.id=NEW.policy_case_id AND c.status IN ('approved','in_progress') AND e.is_demo=NEW.is_demo AND e.point_id=NEW.target_point_id) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Approved matching case required'; END IF;
 END IF;
END$$
DELIMITER ;

-- SIMULASI draft rules: inactive until reviewed/published. No automatic official cases/actions.
INSERT INTO policy_packages(organization_id,code,name,description)
SELECT id,CONCAT('DEMO-STABILISASI-',id),'SIMULASI Stabilisasi harga dan pasokan',
'Kenaikan harga DAN stok rendah; semua rekomendasi membutuhkan persetujuan' FROM organizations;
INSERT INTO policy_versions(package_id,version_no,severity,valid_from,recommendation)
SELECT id,1,'warning','2026-01-01 00:00:00','Verifikasi lapangan dan koordinasi pemasok; bukan otorisasi anggaran'
FROM policy_packages WHERE code LIKE 'DEMO-STABILISASI-%';
INSERT INTO policy_rule_groups(version_id,logic) SELECT id,'AND' FROM policy_versions;
INSERT INTO policy_rule_conditions(group_id,metric,comparator,threshold,baseline,lookback_days,min_observations)
SELECT id,'price_change_pct','gt',10,'days_ago',7,2 FROM policy_rule_groups;
INSERT INTO policy_rule_conditions(group_id,metric,comparator,threshold,baseline,lookback_days,min_observations)
SELECT id,'stock_days','lt',3,'none',0,1 FROM policy_rule_groups;
INSERT INTO policy_targets(version_id,region_id)
SELECT v.id,o.region_id FROM policy_versions v JOIN policy_packages p ON p.id=v.package_id JOIN organizations o ON o.id=p.organization_id;

-- CONTRACT RULE ENGINE (application worker; no claim that storage alone runs rules):
-- 1. Evaluate only published, valid versions; use frozen_definition, not mutable draft tables.
-- 2. Publish transaction validates one root group, no cycles/empty groups, same-version parents,
--    valid target ancestry, approved source data, non-overlapping active versions and permission policy.publish.
-- 3. Freeze full groups/conditions/targets/recommendation JSON, publisher, timestamp; edits => new version.
-- 4. AND: any false=false, else any unknown=unknown, else true. OR: any true=true,
--    else any unknown=unknown, else false. Missing/stale/baseline=0 => insufficient_data, NEVER safe.
-- 5. Compare same commodity quality/base unit/price_type/point; keep observation IDs, actual timestamps,
--    values, baseline selection/tolerance, condition outcomes and coverage in evidence_snapshot.
-- 6. Historical runs use data_cutoff_at, never NOW for freshness; do not mix demo and production.
-- 7. Test runs never create cases. Live matched runs may create pending cases using unique dedupe_key
--    (version+point+commodity+cooldown bucket); record exact rule snapshot. No automatic budget/spending.
-- 8. Permission policy.approve + target scope required before action.assign + matching scope to create tasks.
-- 9. GET/export queries filter fn_can_point(actor,permission,point_id)=TRUE BEFORE aggregation/pagination.
--    Legacy v_public_prices is public-only, not an internal authorization view.
-- 10. Admin access service: access.manage plus internal identity; no self-edit; same organization only;
--     protect last active admin; require reauthentication; append before/after audit in same transaction;
--     invalidate permission cache/sessions on revoke. DB DML only via trusted service principal.
-- 11. Master/market access outside point queries requires analogous ancestor scope filtering in API.
-- 12. partner_accounts identifies account ownership only, never implicitly grants data access.
-- 13. Existing alert_rules/recommendations remain for legacy history; new policy engine uses policy_*.
-- 14. Bootstrap real admin using DBA-reviewed existing internal user + admin role + explicit scopes;
--     no account is auto-activated and no default password is introduced by this migration.
-- 15. New point draft/submission must enforce fn_can_point(author,'report.create',point),
--     report.edit_draft only own draft; revision requires internal + report.revise. All write endpoints
--     transactionally validate permissions/scopes; price/stocks remain unchanged until verification.

-- The replacement sp_verify_report follows below. Do not stop the migration before its completion.

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_verify_report$$
CREATE PROCEDURE sp_verify_report(IN p_report BIGINT UNSIGNED,IN p_actor BIGINT UNSIGNED,IN p_decision VARCHAR(20),IN p_note TEXT)
SQL SECURITY INVOKER
BEGIN
 DECLARE v_series BIGINT UNSIGNED; DECLARE v_lock BIGINT UNSIGNED; DECLARE v_author BIGINT UNSIGNED;
 DECLARE v_prev BIGINT UNSIGNED; DECLARE v_current BIGINT UNSIGNED; DECLARE v_version INT; DECLARE v_demo BOOLEAN;
 DECLARE v_status VARCHAR(20); DECLARE v_point BIGINT UNSIGNED; DECLARE v_observed DATETIME;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 START TRANSACTION;
 IF NOT fn_has_permission(p_actor,'report.verify') OR NOT EXISTS(SELECT 1 FROM users WHERE id=p_actor AND is_internal=TRUE) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Internal user with verify permission required'; END IF;
 IF p_decision IS NULL OR p_decision NOT IN ('approved','returned','rejected') OR p_note IS NULL OR CHAR_LENGTH(TRIM(p_note))=0 THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Decision and note required'; END IF;
 SELECT series_id INTO v_series FROM reports WHERE id=p_report;
 IF v_series IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Report not found'; END IF;
 SELECT id,point_id,observed_at INTO v_lock,v_point,v_observed FROM report_series WHERE id=v_series FOR UPDATE;
 SELECT status,submitted_by,previous_report_id,version_no,is_demo INTO v_status,v_author,v_prev,v_version,v_demo FROM reports WHERE id=p_report FOR UPDATE;
 IF NOT fn_can_point(p_actor,'report.verify',v_point) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Verification target outside scope'; END IF;
 IF v_status<>'submitted' OR v_author=p_actor THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Report not submitted or self approval forbidden'; END IF;
 IF p_decision='approved' THEN
 IF NOT fn_can_point(v_author,'report.create',v_point) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Author input permission or point scope revoked'; END IF;
 IF NOT EXISTS(SELECT 1 FROM report_items WHERE report_id=p_report) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Empty report'; END IF;
 IF v_demo=FALSE AND EXISTS(SELECT 1 FROM reports r JOIN data_sources ds ON ds.id=r.source_id JOIN report_series s ON s.id=r.series_id JOIN monitoring_points p ON p.id=s.point_id
 WHERE r.id=p_report AND (ds.source_type='simulation' OR p.is_demo=TRUE)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Simulation cannot become official data'; END IF;
 SET v_current=NULL;
 SELECT id INTO v_current FROM reports WHERE series_id=v_series AND status='approved' FOR UPDATE;
 IF v_current IS NULL AND (v_prev IS NOT NULL OR v_version<>1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Initial approved version must be version 1'; END IF;
 IF v_current IS NOT NULL THEN
 IF v_prev IS NULL OR v_prev<>v_current OR NOT EXISTS(SELECT 1 FROM reports WHERE id=v_current AND version_no=v_version-1 AND is_demo=v_demo) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Revision must reference current approved version'; END IF;
 IF NOT fn_can_point(v_author,'report.revise',v_point) OR NOT EXISTS(SELECT 1 FROM users WHERE id=v_author AND is_internal=TRUE) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Only internal operator proposes official revisions'; END IF;
 IF EXISTS(SELECT 1 FROM report_items oldi WHERE oldi.report_id=v_current AND NOT EXISTS(SELECT 1 FROM report_items newi WHERE newi.report_id=p_report AND newi.commodity_id=oldi.commodity_id)) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Revision must include full previous commodity set'; END IF;
 UPDATE reports SET status='superseded' WHERE id=v_current;
 END IF;
 END IF;
 UPDATE reports SET status=p_decision,verified_by=IF(p_decision='approved',p_actor,NULL),verified_at=IF(p_decision='approved',CURRENT_TIMESTAMP,NULL) WHERE id=p_report;
 INSERT INTO verification_events(report_id,actor_id,decision,note) VALUES(p_report,p_actor,p_decision,p_note);
 INSERT INTO audit_logs(actor_id,entity_type,entity_id,action,old_values,new_values)
 VALUES(p_actor,'reports',p_report,p_decision,JSON_OBJECT('status',v_status),JSON_OBJECT('status',p_decision));
 COMMIT;
END$$
CREATE TRIGGER bu_policy_versions BEFORE UPDATE ON policy_versions FOR EACH ROW
BEGIN
 IF OLD.state<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Published policy immutable; create a new version'; END IF;
END$$
CREATE TRIGGER bd_policy_versions BEFORE DELETE ON policy_versions FOR EACH ROW
BEGIN
 IF OLD.state<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Published policy cannot be deleted'; END IF;
END$$
CREATE TRIGGER bi_policy_rule_groups BEFORE INSERT ON policy_rule_groups FOR EACH ROW
BEGIN
 IF (SELECT state FROM policy_versions WHERE id=NEW.version_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END$$
CREATE TRIGGER bu_policy_rule_groups BEFORE UPDATE ON policy_rule_groups FOR EACH ROW
BEGIN
 IF (SELECT state FROM policy_versions WHERE id=OLD.version_id)<>'draft' OR (SELECT state FROM policy_versions WHERE id=NEW.version_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END$$
CREATE TRIGGER bd_policy_rule_groups BEFORE DELETE ON policy_rule_groups FOR EACH ROW
BEGIN
 IF (SELECT state FROM policy_versions WHERE id=OLD.version_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END$$
CREATE TRIGGER bi_policy_rule_conditions BEFORE INSERT ON policy_rule_conditions FOR EACH ROW
BEGIN
 IF (SELECT v.state FROM policy_rule_groups g JOIN policy_versions v ON v.id=g.version_id WHERE g.id=NEW.group_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END$$
CREATE TRIGGER bu_policy_rule_conditions BEFORE UPDATE ON policy_rule_conditions FOR EACH ROW
BEGIN
 IF (SELECT v.state FROM policy_rule_groups g JOIN policy_versions v ON v.id=g.version_id WHERE g.id=OLD.group_id)<>'draft' OR (SELECT v.state FROM policy_rule_groups g JOIN policy_versions v ON v.id=g.version_id WHERE g.id=NEW.group_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END$$
CREATE TRIGGER bd_policy_rule_conditions BEFORE DELETE ON policy_rule_conditions FOR EACH ROW
BEGIN
 IF (SELECT v.state FROM policy_rule_groups g JOIN policy_versions v ON v.id=g.version_id WHERE g.id=OLD.group_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END$$
CREATE TRIGGER bi_policy_targets BEFORE INSERT ON policy_targets FOR EACH ROW
BEGIN
 IF (SELECT state FROM policy_versions WHERE id=NEW.version_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END$$
CREATE TRIGGER bu_policy_targets BEFORE UPDATE ON policy_targets FOR EACH ROW
BEGIN
 IF (SELECT state FROM policy_versions WHERE id=OLD.version_id)<>'draft' OR (SELECT state FROM policy_versions WHERE id=NEW.version_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END$$
CREATE TRIGGER bd_policy_targets BEFORE DELETE ON policy_targets FOR EACH ROW
BEGIN
 IF (SELECT state FROM policy_versions WHERE id=OLD.version_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END$$
DELIMITER ;
INSERT INTO schema_migrations(version,notes) VALUES('2026_10_27_v2','7 menus; per-user permissions; scoped access; policy packages; verifier updated');
SELECT version,applied_at FROM schema_migrations;
SELECT id,code,name,sort_order FROM app_menus ORDER BY sort_order;
SELECT COUNT(*) AS migrated_scope_rows FROM user_data_scopes;
-- All disabled demo users should have zero effective permissions.
SELECT COUNT(*) AS active_effective_permission_rows FROM v_effective_user_permissions;
-- Check smoke tests in staging with a NEW temporary active internal test account:
-- deny override beats role grant; no scope => denied; market A scope does not allow market B;
-- region scope includes descendant villages; point scope does not include neighbouring points;
-- revoked/expired scope => denied; read scope never grants create scope;
-- own submitted report verification => error; inactive author => error pending re-assignment;
-- official report items immutable; published rules immutable;
-- draft/test policy cannot authorize actions; policy_case mismatched point => error.

