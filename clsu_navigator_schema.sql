CREATE DATABASE IF NOT EXISTS clsu_navigator_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE clsu_navigator_db;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS building_map_geometries;
DROP TABLE IF EXISTS map_feature_geometries;
DROP TABLE IF EXISTS building_categories;
DROP TABLE IF EXISTS map_features;
DROP TABLE IF EXISTS map_categories;
DROP TABLE IF EXISTS map_assets;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS event_venues;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS event_categories;
DROP TABLE IF EXISTS floorplan_signatures;
DROP TABLE IF EXISTS floorplan_approvals;
DROP TABLE IF EXISTS floorplan_access_grants;
DROP TABLE IF EXISTS floorplan_access_requests;
DROP TABLE IF EXISTS floorplan_elements;
DROP TABLE IF EXISTS floorplan_versions;
DROP TABLE IF EXISTS floorplans;
DROP TABLE IF EXISTS amenities;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS floors;
DROP TABLE IF EXISTS buildings;
DROP TABLE IF EXISTS user_department_assignments;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE roles (
    role_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE departments (
    department_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(150) NOT NULL,
    department_code VARCHAR(30) NULL UNIQUE,
    parent_department_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_departments_parent
        FOREIGN KEY (parent_department_id) REFERENCES departments(department_id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- Map display assets: the Leaflet, detailed SVG, and black-and-white flat options.
-- Store files on the server or storage service; keep only their paths and metadata here.
CREATE TABLE map_assets (
    map_asset_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    map_name VARCHAR(150) NOT NULL,
    map_type ENUM('Leaflet','SVG','Flat') NOT NULL,
    description VARCHAR(255) NULL,
    asset_path VARCHAR(500) NULL,
    asset_format ENUM('Tiles','GeoJSON','SVG','Image','JSON','Other') NOT NULL DEFAULT 'Other',
    coordinate_system ENUM('Geographic','Image','SVG','Local') NOT NULL DEFAULT 'Geographic',
    width_px INT UNSIGNED NULL,
    height_px INT UNSIGNED NULL,
    metadata JSON NULL,
    is_default BOOLEAN NOT NULL DEFAULT FALSE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_map_assets_type_active (map_type, is_active)
) ENGINE=InnoDB;

-- Reusable categories for map filters. College names/codes should be added as data
-- only after the official campus list has been confirmed.
CREATE TABLE map_categories (
    category_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filter_group ENUM('Amenity','Facility','College Building') NOT NULL,
    category_name VARCHAR(120) NOT NULL,
    description VARCHAR(255) NULL,
    icon_name VARCHAR(80) NULL,
    display_order INT NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_map_category_group_name (filter_group, category_name),
    INDEX idx_map_categories_group_active (filter_group, is_active, display_order)
) ENGINE=InnoDB;

CREATE TABLE users (
    user_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    account_status ENUM('Pending','Active','Suspended','Deactivated') NOT NULL DEFAULT 'Pending',
    email_verified_at DATETIME NULL,
    last_login_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id) REFERENCES roles(role_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_users_department
        FOREIGN KEY (department_id) REFERENCES departments(department_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_users_role_status (role_id, account_status),
    INDEX idx_users_department (department_id)
) ENGINE=InnoDB;

CREATE TABLE user_department_assignments (
    assignment_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NOT NULL,
    assignment_type ENUM('Primary','Administrative','Faculty','Staff','Other') NOT NULL DEFAULT 'Other',
    is_primary BOOLEAN NOT NULL DEFAULT FALSE,
    start_date DATE NULL,
    end_date DATE NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_uda_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_uda_department
        FOREIGN KEY (department_id) REFERENCES departments(department_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY uq_user_department_assignment (user_id, department_id),
    INDEX idx_uda_department (department_id)
) ENGINE=InnoDB;

CREATE TABLE permissions (
    permission_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    permission_code VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE role_permissions (
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    CONSTRAINT fk_rp_role
        FOREIGN KEY (role_id) REFERENCES roles(role_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_rp_permission
        FOREIGN KEY (permission_id) REFERENCES permissions(permission_id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE buildings (
    building_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    department_id BIGINT UNSIGNED NULL,
    building_name VARCHAR(150) NOT NULL,
    building_code VARCHAR(30) NULL UNIQUE,
    description TEXT NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_buildings_department
        FOREIGN KEY (department_id) REFERENCES departments(department_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_buildings_name (building_name),
    INDEX idx_buildings_department (department_id)
) ENGINE=InnoDB;

-- Roads and land areas that are not represented by building records.
CREATE TABLE map_features (
    map_feature_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    feature_name VARCHAR(180) NOT NULL,
    feature_type ENUM('Road','Land','Other') NOT NULL,
    description TEXT NULL,
    properties JSON NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_map_features_type_active (feature_type, is_active),
    INDEX idx_map_features_name (feature_name)
) ENGINE=InnoDB;

-- Geometry is separated by map asset because geographic coordinates, SVG paths,
-- and simplified flat-map geometry may use different coordinate systems.
CREATE TABLE map_feature_geometries (
    geometry_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    map_feature_id BIGINT UNSIGNED NOT NULL,
    map_asset_id BIGINT UNSIGNED NOT NULL,
    geometry_format ENUM('GeoJSON','SVGPath','ImageCoordinates','Other') NOT NULL,
    geometry_data JSON NULL,
    svg_path_data LONGTEXT NULL,
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_mfg_feature
        FOREIGN KEY (map_feature_id) REFERENCES map_features(map_feature_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_mfg_asset
        FOREIGN KEY (map_asset_id) REFERENCES map_assets(map_asset_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY uq_map_feature_asset (map_feature_id, map_asset_id),
    INDEX idx_mfg_asset (map_asset_id)
) ENGINE=InnoDB;

CREATE TABLE building_map_geometries (
    building_geometry_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    building_id BIGINT UNSIGNED NOT NULL,
    map_asset_id BIGINT UNSIGNED NOT NULL,
    geometry_format ENUM('GeoJSON','SVGPath','ImageCoordinates','Other') NOT NULL,
    geometry_data JSON NULL,
    svg_path_data LONGTEXT NULL,
    label_x DECIMAL(12,4) NULL,
    label_y DECIMAL(12,4) NULL,
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bmg_building
        FOREIGN KEY (building_id) REFERENCES buildings(building_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_bmg_asset
        FOREIGN KEY (map_asset_id) REFERENCES map_assets(map_asset_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY uq_building_map_asset (building_id, map_asset_id),
    INDEX idx_bmg_asset (map_asset_id)
) ENGINE=InnoDB;

CREATE TABLE building_categories (
    building_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (building_id, category_id),
    CONSTRAINT fk_bc_building
        FOREIGN KEY (building_id) REFERENCES buildings(building_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_bc_category
        FOREIGN KEY (category_id) REFERENCES map_categories(category_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_bc_category (category_id)
) ENGINE=InnoDB;

CREATE TABLE floors (
    floor_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    building_id BIGINT UNSIGNED NOT NULL,
    floor_name VARCHAR(100) NOT NULL,
    floor_number INT NULL,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_floors_building
        FOREIGN KEY (building_id) REFERENCES buildings(building_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY uq_building_floor (building_id, floor_name),
    INDEX idx_floors_building (building_id)
) ENGINE=InnoDB;

CREATE TABLE rooms (
    room_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    floor_id BIGINT UNSIGNED NOT NULL,
    room_code VARCHAR(50) NOT NULL,
    room_name VARCHAR(150) NULL,
    room_type VARCHAR(80) NULL,
    description TEXT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rooms_floor
        FOREIGN KEY (floor_id) REFERENCES floors(floor_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY uq_floor_room_code (floor_id, room_code),
    INDEX idx_rooms_code (room_code)
) ENGINE=InnoDB;

CREATE TABLE amenities (
    amenity_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NULL,
    building_id BIGINT UNSIGNED NULL,
    amenity_name VARCHAR(120) NOT NULL,
    description TEXT NULL,
    icon_name VARCHAR(80) NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    map_position JSON NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_amenities_category
        FOREIGN KEY (category_id) REFERENCES map_categories(category_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_amenities_building
        FOREIGN KEY (building_id) REFERENCES buildings(building_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_amenities_category (category_id),
    INDEX idx_amenities_building (building_id),
    INDEX idx_amenities_name (amenity_name)
) ENGINE=InnoDB;

CREATE TABLE floorplans (
    floorplan_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    floor_id BIGINT UNSIGNED NOT NULL,
    plan_name VARCHAR(180) NOT NULL,
    plan_type ENUM('A','B') NOT NULL,
    description TEXT NULL,
    access_classification ENUM('Public','Restricted','Highly Restricted') NOT NULL DEFAULT 'Restricted',
    current_version_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_floorplans_floor
        FOREIGN KEY (floor_id) REFERENCES floors(floor_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_floorplans_creator
        FOREIGN KEY (created_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_floorplans_type_floor (plan_type, floor_id),
    INDEX idx_floorplans_classification (access_classification)
) ENGINE=InnoDB;

CREATE TABLE floorplan_versions (
    version_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    floorplan_id BIGINT UNSIGNED NOT NULL,
    version_number INT UNSIGNED NOT NULL,
    file_key VARCHAR(500) NOT NULL,
    original_file_name VARCHAR(255) NULL,
    mime_type VARCHAR(120) NULL,
    file_size_bytes BIGINT UNSIGNED NULL,
    file_hash CHAR(64) NULL,
    change_summary TEXT NULL,
    revision_status ENUM('Draft','Pending Review','Approved','Rejected','Archived') NOT NULL DEFAULT 'Draft',
    uploaded_by BIGINT UNSIGNED NOT NULL,
    uploaded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    approved_at DATETIME NULL,
    approved_by BIGINT UNSIGNED NULL,
    CONSTRAINT fk_fpv_floorplan
        FOREIGN KEY (floorplan_id) REFERENCES floorplans(floorplan_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_fpv_uploader
        FOREIGN KEY (uploaded_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_fpv_approver
        FOREIGN KEY (approved_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY uq_floorplan_version (floorplan_id, version_number),
    INDEX idx_fpv_status (revision_status, uploaded_at)
) ENGINE=InnoDB;

ALTER TABLE floorplans
    ADD CONSTRAINT fk_floorplans_current_version
    FOREIGN KEY (current_version_id) REFERENCES floorplan_versions(version_id)
    ON UPDATE CASCADE ON DELETE SET NULL;

CREATE TABLE floorplan_elements (
    element_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    floorplan_id BIGINT UNSIGNED NOT NULL,
    room_id BIGINT UNSIGNED NULL,
    amenity_id BIGINT UNSIGNED NULL,
    element_type ENUM('Room','Amenity','Entrance','Exit','Elevator','Stairs','Ramp','Restroom','Other') NOT NULL,
    element_label VARCHAR(150) NULL,
    position_data JSON NULL,
    display_order INT NOT NULL DEFAULT 0,
    is_visible BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_fe_floorplan
        FOREIGN KEY (floorplan_id) REFERENCES floorplans(floorplan_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_fe_room
        FOREIGN KEY (room_id) REFERENCES rooms(room_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_fe_amenity
        FOREIGN KEY (amenity_id) REFERENCES amenities(amenity_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_fe_floorplan (floorplan_id, display_order)
) ENGINE=InnoDB;

CREATE TABLE floorplan_access_requests (
    request_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    floorplan_id BIGINT UNSIGNED NOT NULL,
    requester_id BIGINT UNSIGNED NOT NULL,
    purpose TEXT NOT NULL,
    request_status ENUM('Pending','Approved','Rejected','Cancelled','Expired') NOT NULL DEFAULT 'Pending',
    reviewed_by BIGINT UNSIGNED NULL,
    review_notes TEXT NULL,
    requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_at DATETIME NULL,
    CONSTRAINT fk_far_floorplan
        FOREIGN KEY (floorplan_id) REFERENCES floorplans(floorplan_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_far_requester
        FOREIGN KEY (requester_id) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_far_reviewer
        FOREIGN KEY (reviewed_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_far_status (request_status, requested_at),
    INDEX idx_far_requester (requester_id)
) ENGINE=InnoDB;

CREATE TABLE floorplan_access_grants (
    grant_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    floorplan_id BIGINT UNSIGNED NOT NULL,
    granted_by BIGINT UNSIGNED NOT NULL,
    granted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NULL,
    revoked_at DATETIME NULL,
    revoked_by BIGINT UNSIGNED NULL,
    grant_status ENUM('Active','Revoked','Expired') NOT NULL DEFAULT 'Active',
    CONSTRAINT fk_fag_request
        FOREIGN KEY (request_id) REFERENCES floorplan_access_requests(request_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_fag_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_fag_floorplan
        FOREIGN KEY (floorplan_id) REFERENCES floorplans(floorplan_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_fag_granted_by
        FOREIGN KEY (granted_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_fag_revoked_by
        FOREIGN KEY (revoked_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_fag_user_plan_status (user_id, floorplan_id, grant_status),
    INDEX idx_fag_expiry (expires_at)
) ENGINE=InnoDB;

CREATE TABLE floorplan_approvals (
    approval_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    version_id BIGINT UNSIGNED NOT NULL,
    reviewer_id BIGINT UNSIGNED NOT NULL,
    decision ENUM('Pending','Approved','Rejected','Returned for Revision') NOT NULL DEFAULT 'Pending',
    remarks TEXT NULL,
    reviewed_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_fpa_version
        FOREIGN KEY (version_id) REFERENCES floorplan_versions(version_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_fpa_reviewer
        FOREIGN KEY (reviewer_id) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_fpa_version_decision (version_id, decision)
) ENGINE=InnoDB;

CREATE TABLE floorplan_signatures (
    signature_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    version_id BIGINT UNSIGNED NOT NULL,
    signatory_name VARCHAR(150) NOT NULL,
    signatory_position VARCHAR(150) NOT NULL,
    signature_file_key VARCHAR(500) NULL,
    signed_at DATETIME NULL,
    verification_status ENUM('Not Verified','Verified','Rejected') NOT NULL DEFAULT 'Not Verified',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_fps_version
        FOREIGN KEY (version_id) REFERENCES floorplan_versions(version_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_fps_version (version_id)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    audit_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    details JSON NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_audit_user_date (user_id, created_at),
    INDEX idx_audit_entity (entity_type, entity_id),
    INDEX idx_audit_action_date (action, created_at)
) ENGINE=InnoDB;

CREATE TABLE event_categories (
    category_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE events (
    event_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NULL,
    organizer_department_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    event_title VARCHAR(200) NOT NULL,
    event_description TEXT NULL,
    event_start DATETIME NOT NULL,
    event_end DATETIME NOT NULL,
    registration_start DATETIME NULL,
    registration_end DATETIME NULL,
    event_status ENUM('Draft','Pending Approval','Published','Cancelled','Completed','Archived') NOT NULL DEFAULT 'Draft',
    visibility ENUM('Public','Campus Only','Restricted') NOT NULL DEFAULT 'Public',
    contact_name VARCHAR(150) NULL,
    contact_email VARCHAR(190) NULL,
    contact_phone VARCHAR(40) NULL,
    poster_file_key VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_events_category FOREIGN KEY (category_id) REFERENCES event_categories(category_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_events_department FOREIGN KEY (organizer_department_id) REFERENCES departments(department_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_events_creator FOREIGN KEY (created_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_event_dates CHECK (event_end > event_start),
    CONSTRAINT chk_event_registration_dates CHECK (
        registration_start IS NULL OR registration_end IS NULL OR registration_end >= registration_start
    ),
    INDEX idx_events_schedule (event_status, event_start, event_end),
    INDEX idx_events_visibility (visibility, event_status, event_start),
    INDEX idx_events_department (organizer_department_id),
    INDEX idx_events_category (category_id)
) ENGINE=InnoDB;

CREATE TABLE event_venues (
    event_venue_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL,
    building_id BIGINT UNSIGNED NOT NULL,
    floor_id BIGINT UNSIGNED NULL,
    room_id BIGINT UNSIGNED NULL,
    venue_name VARCHAR(200) NULL,
    venue_notes TEXT NULL,
    is_primary BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_event_venues_event FOREIGN KEY (event_id) REFERENCES events(event_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_event_venues_building FOREIGN KEY (building_id) REFERENCES buildings(building_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_event_venues_floor FOREIGN KEY (floor_id) REFERENCES floors(floor_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_event_venues_room FOREIGN KEY (room_id) REFERENCES rooms(room_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_event_venues_event (event_id),
    INDEX idx_event_venues_building (building_id),
    INDEX idx_event_venues_room (room_id)
) ENGINE=InnoDB;

INSERT INTO roles (role_name, description) VALUES
('Super Admin', 'Maintains the system and has system-wide administrative access.'),
('Admin', 'Manages authorized information for an assigned department. SPIMO Admin has additional Type B access.'),
('Faculty Member', 'Faculty user; may approve Type A access requests only when specifically authorized.'),
('Staff', 'Staff user with access according to assigned department and permissions.'),
('Student', 'Student user who can view permitted Type A plans and request restricted Type A access.'),
('Guest', 'Guest user limited to public campus information; cannot view interior floor plan diagrams.');

INSERT INTO map_categories (filter_group, category_name, description, display_order) VALUES
('Amenity', 'Academic Support', 'Amenities related to academic assistance and learning support.', 1),
('Amenity', 'Sports & Recreation', 'Amenities for sports, exercise, and recreation.', 2),
('Amenity', 'Health & Wellbeing', 'Amenities related to health, wellness, and personal care.', 3),
('Amenity', 'Daily Living & Dining', 'Amenities for everyday needs, food, and dining.', 4),
('Facility', 'Academic & Instructional Facilities', 'Facilities used for teaching, learning, and instruction.', 1),
('Facility', 'Research & Innovation Facilities', 'Facilities used for research, development, and innovation.', 2),
('Facility', 'Residential & Living Facilities', 'Facilities used for residence and campus living.', 3),
('Facility', 'Administrative & Student Support Facilities', 'Facilities for administration and student support.', 4),
('Facility', 'Athletic & Recreational Facilities', 'Facilities for athletics, sports, and recreation.', 5),
('Facility', 'Institutional Support & Infrastructure', 'Facilities supporting campus operations and infrastructure.', 6);

INSERT INTO permissions (permission_code, description) VALUES
('users.manage', 'Create, update, deactivate, and manage user accounts.'),
('roles.manage', 'Manage roles and role permissions.'),
('departments.manage', 'Manage departments and organizational units.'),
('buildings.manage', 'Manage campus building records.'),
('map_assets.manage', 'Manage map versions and map asset references.'),
('map_features.manage', 'Manage road, land, and other map feature records.'),
('map_categories.manage', 'Manage map filter categories.'),
('floors.manage', 'Manage floor records.'),
('rooms.manage', 'Manage room records.'),
('amenities.manage', 'Manage amenities and accessibility features.'),
('type_a.view_public', 'View Type A plans classified for public or generally permitted viewing.'),
('type_a.manage_department', 'Manage Type A plans within an authorized department.'),
('type_a.view_restricted', 'View a restricted Type A plan when authorized.'),
('type_a.approve_access', 'Approve access requests for restricted Type A plans when assigned.'),
('type_b.view', 'View official Type B technical plans.'),
('type_b.manage', 'Upload and manage official Type B technical plans.'),
('type_b.approve', 'Approve official Type B technical plan versions.'),
('type_b.history.view', 'View Type B version and revision history.'),
('audit.view', 'View audit logs.'),
('access_requests.manage', 'Review and process floor plan access requests.'),
('events.view_public', 'View published public campus events.'),
('events.create', 'Create campus event records.'),
('events.manage_department', 'Manage events organized by an assigned department.'),
('events.approve', 'Approve events for publication.'),
('events.manage_all', 'Manage all campus events.');

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.role_id, p.permission_id
FROM roles r
JOIN permissions p ON
    (r.role_name = 'Super Admin' AND p.permission_code IN (
        'users.manage','roles.manage','departments.manage','buildings.manage','map_assets.manage',
        'map_features.manage','map_categories.manage','floors.manage',
        'rooms.manage','amenities.manage','type_a.view_public','type_a.manage_department',
        'type_a.view_restricted','type_a.approve_access','type_b.view','type_b.manage',
        'type_b.approve','type_b.history.view','audit.view','access_requests.manage',
        'events.view_public','events.create','events.manage_department','events.approve','events.manage_all'
    ))
 OR (r.role_name = 'Admin' AND p.permission_code IN (
        'buildings.manage','map_features.manage','map_categories.manage','floors.manage','rooms.manage','amenities.manage',
        'type_a.view_public','type_a.manage_department','type_a.view_restricted',
        'access_requests.manage','events.view_public','events.create','events.manage_department'
    ))
 OR (r.role_name = 'Faculty Member' AND p.permission_code IN (
        'type_a.view_public','events.view_public'
    ))
 OR (r.role_name = 'Staff' AND p.permission_code IN (
        'type_a.view_public','events.view_public'
    ))
 OR (r.role_name = 'Student' AND p.permission_code IN (
        'type_a.view_public','events.view_public'
    ))
 OR (r.role_name = 'Guest' AND p.permission_code IN (
        'type_a.view_public','events.view_public'
    ));
    