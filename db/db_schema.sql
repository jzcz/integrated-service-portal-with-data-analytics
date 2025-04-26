DROP DATABASE IF EXISTS ISPDA_DB;

CREATE DATABASE ISPDA_DB;

USE ISPDA_DB;

CREATE TABLE programs (
  program_id INT PRIMARY KEY AUTO_INCREMENT,
  program_name VARCHAR(150) NOT NULL
);

CREATE TABLE user (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(250) NOT NULL,
  role ENUM('Admin', 'Counselor') NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  is_deleted BOOLEAN DEFAULT false,
  is_disabled BOOLEAN DEFAULT false
);

CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE,
    user_id INT NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) NOT NULL,
    suffix ENUM('Sr.', 'Jr.', 'III', 'IV'),
    student_no CHAR(9) NOT NULL UNIQUE,
    program_id INT,
    current_year_level ENUM('1ST', '2ND', '3RD', '4TH'),
    campus VARCHAR(150),
    gender ENUM('Male', 'Female'),
    birthdate DATE NOT NULL,
    img_url VARCHAR(250),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    agreedToDataPrivacyPolicy BOOLEAN NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);

CREATE TABLE counselors (
  counselor_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL UNIQUE,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  middle_name VARCHAR(100),
  suffix ENUM('Sr.', 'Jr.', 'III', 'IV'),
  img_url VARCHAR(250),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  employee_id VARCHAR(25) NOT NULL UNIQUE,
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);

CREATE TABLE admin (
  admin_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL UNIQUE,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  middle_name VARCHAR(100),
  suffix ENUM('Sr.', 'Jr.', 'III', 'IV'),
  img_url VARCHAR(250),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  employee_id VARCHAR(25) NOT NULL UNIQUE,
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);

CREATE TABLE announcements (
  announcement_id INT PRIMARY KEY AUTO_INCREMENT,
  title TEXT NOT NULL,
  description TEXT NOT NULL,
  img_url VARCHAR(250),
  created_by INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  is_archived BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (created_by) REFERENCES counselors(counselor_id)
);

CREATE TABLE appt_attendee (
  attendee_id INT PRIMARY KEY AUTO_INCREMENT,
  student_id INT, -- for appointments requested online
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  middle_name VARCHAR(100),
  suffix ENUM('Sr.', 'Jr.', 'III', 'IV'),
  student_no CHAR(9) NOT NULL,
  program_id INT,
  current_year_level ENUM('1st', '2nd', '3rd', '4th'),
  gender ENUM('Male', 'Female'),
  personal_contact_no CHAR(11) NOT NULL,
  student_email VARCHAR(150) NOT NULL,
  guardian_name VARCHAR(100) NOT NULL,
  guardian_contact_no VARCHAR(150) NOT NULL,
  FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE SET NULL,
  FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE SET NULL
);

CREATE TABLE appointments (
  appt_id INT PRIMARY KEY AUTO_INCREMENT,
  attendee_id INT,
  counseling_concern ENUM('Career', 'Academic', 'Personal') NOT NULL,
  status ENUM('Pending', 'Upcoming', 'Declined', 'Cancelled', 'Completed') NOT NULL,
  preferred_day ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday') NOT NULL,
  preferred_time ENUM(
    '9:00 AM to 10:00 AM', 
    '11:00 AM to 12:00 NN', 
    '1:00 PM to 2:00 PM', 
    '3:00 PM to 4:00 PM'
  ) NOT NULL,
  appt_date DATE,
  appt_start_time TIME,
  appt_end_time TIME,
  appt_req_type ENUM('Online', 'Walk-in'),
  cancellation_reason TEXT,
  decline_reason TEXT,
  agreedToTermsAndConditions BOOLEAN NOT NULL,
  agreedToDataPrivacyPolicy BOOLEAN NOT NULL,
  agreedToLimitations BOOLEAN NOT NULL,
  FOREIGN KEY (attendee_id) REFERENCES appt_attendee(attendee_id)
);

CREATE TABLE good_moral_cert_reqs (
  gmc_req_id INT PRIMARY KEY AUTO_INCREMENT,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  middle_name VARCHAR(100),
  suffix ENUM('Sr.', 'Jr.', 'III', 'IV'),
  student_no CHAR(9) NOT NULL,
  program_id INT NOT NULL,
  start_school_year VARCHAR(12) NOT NULL,
  last_school_year VARCHAR(12) NOT NULL,
  semester ENUM('1st', '2nd') NOT NULL,
  status ENUM('Pending', 'Approved', 'Completed', 'Cancelled', 'Declined', 'For Pickup') NOT NULL,
  reason_desc TEXT NOT NULL,
  proof_img_url VARCHAR(100) NOT NULL,
  decline_reason TEXT,
  additional_req_des TEXT,
  pickup_date DATE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updated_by INT,
  FOREIGN KEY (program_id) REFERENCES programs(program_id),
  FOREIGN KEY (updated_by) REFERENCES counselors(counselor_id)
);

CREATE TABLE publication_materials (
  pub_mat_id INT PRIMARY KEY AUTO_INCREMENT,
  file_title VARCHAR(250) NOT NULL,
  file_desc TEXT,
  file_url VARCHAR(250) NOT NULL,
  cover_img_url VARCHAR(250),
  type ENUM('Module', 'Infographics') NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updated_by INT,
  uploaded_by INT,
  FOREIGN KEY (updated_by) REFERENCES counselors(counselor_id) ON DELETE SET NULL,
  FOREIGN KEY (uploaded_by) REFERENCES counselors(counselor_id) ON DELETE SET NULL
);

CREATE TABLE surveys (
  survey_id INT PRIMARY KEY AUTO_INCREMENT,
  survey_title VARCHAR(250) NOT NULL,
  survey_desc TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by INT,
  is_archived BOOLEAN DEFAULT FALSE,
  is_deleted BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (created_by) REFERENCES counselors(counselor_id) ON DELETE SET NULL
);

CREATE TABLE survey_questions (
  survey_question_id INT PRIMARY KEY AUTO_INCREMENT,
  survey_id INT NOT NULL,
  question_text TEXT NOT NULL,
  FOREIGN KEY (survey_id) REFERENCES surveys(survey_id) ON DELETE CASCADE
);

CREATE TABLE survey_options (
  survey_option_id INT PRIMARY KEY AUTO_INCREMENT,
  survey_id INT NOT NULL,
  survey_question_id INT NOT NULL,
  option_text TEXT NOT NULL,
  likert_scale_value INT NOT NULL,
  FOREIGN KEY (survey_id) REFERENCES surveys(survey_id) ON DELETE CASCADE,
  FOREIGN KEY (survey_question_id) REFERENCES survey_questions(survey_question_id) ON DELETE CASCADE
);

CREATE TABLE survey_responses (
  survey_response_id INT PRIMARY KEY AUTO_INCREMENT,
  survey_id INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (survey_id) REFERENCES surveys(survey_id) ON DELETE CASCADE
);

CREATE TABLE survey_answers (
  survey_answer_id INT PRIMARY KEY AUTO_INCREMENT,
  survey_response_id INT NOT NULL,
  survey_option_id INT NOT NULL,
  survey_question_id INT NOT NULL,
  survey_id INT NOT NULL,
  FOREIGN KEY (survey_response_id) REFERENCES survey_responses(survey_response_id) ON DELETE CASCADE,
  FOREIGN KEY (survey_option_id) REFERENCES survey_options(survey_option_id) ON DELETE CASCADE,
  FOREIGN KEY (survey_question_id) REFERENCES survey_questions(survey_question_id) ON DELETE CASCADE,
  FOREIGN KEY (survey_id) REFERENCES surveys(survey_id) ON DELETE CASCADE
);

CREATE TABLE assessments (
  assessment_id INT PRIMARY KEY AUTO_INCREMENT,
  assessment_title VARCHAR(250),
  assessment_desc TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by INT NOT NULL,
  is_archived BOOLEAN DEFAULT FALSE,
  is_deleted BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (created_by) REFERENCES counselors(counselor_id) ON DELETE CASCADE
);

CREATE TABLE assessment_questions (
  assessment_question_id INT PRIMARY KEY AUTO_INCREMENT,
  assessment_id INT NOT NULL,
  question_title TEXT,
  question_text TEXT NOT NULL,
  FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE CASCADE
);

CREATE TABLE assessment_options (
  assessment_option_id INT PRIMARY KEY AUTO_INCREMENT,
  assessment_id INT NOT NULL,
  assessment_question_id INT NOT NULL,
  option_text TEXT NOT NULL,
  likert_scale_value INT,
  FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE CASCADE,
  FOREIGN KEY (assessment_question_id) REFERENCES assessment_questions(assessment_question_id) ON DELETE CASCADE
);

CREATE TABLE assessment_responses (
  assessment_response_id INT PRIMARY KEY AUTO_INCREMENT,
  assessment_id INT NOT NULL,
  student_id INT NOT NULL UNIQUE, -- Ensures one-to-one relationship between student and assessment response
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

CREATE TABLE assessment_answers (
  assessment_answer_id INT PRIMARY KEY AUTO_INCREMENT,
  assessment_response_id INT NOT NULL,
  assessment_option_id INT NOT NULL,
  assessment_question_id INT NOT NULL, -- Renamed for consistency
  assessment_id INT NOT NULL,
  FOREIGN KEY (assessment_response_id) REFERENCES assessment_responses(assessment_response_id) ON DELETE CASCADE,
  FOREIGN KEY (assessment_option_id) REFERENCES assessment_options(assessment_option_id) ON DELETE CASCADE,
  FOREIGN KEY (assessment_question_id) REFERENCES assessment_questions(assessment_question_id) ON DELETE CASCADE,
  FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE CASCADE
);

INSERT INTO programs (program_name)
VALUES 
('Bachelor of Science in Accountancy (BSA)'),
('Bachelor of Science in Management Accounting (BSMA)'),
('Bachelor of Science in Information Technology (BSIT)'),
('Bachelor of Science in Entrepreneurship (BSENT)'),
('Bachelor of Science in Electronics Engineering (BSEcE)'),
('Bachelor of Science in Industrial Engineering (BSIE)'),
('Bachelor of Early Childhood Education (BECED)'),
('Bachelor of Science in Information Systems (BSIS)'),
('Bachelor of Science in Computer Science (BSCS)'),
('Bachelor of Science in Computer Engineering (BSCpE)');