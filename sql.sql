create table courses
(
  id   int auto_increment
    primary key,
  name varchar(255) not null,
  constraint UNIQ_A9A55A4C5E237E06
  unique (name)
)
  collate = utf8_unicode_ci;

create table roles
(
  id   int auto_increment
    primary key,
  name varchar(255) not null,
  constraint UNIQ_B63E2EC75E237E06
  unique (name)
)
  collate = utf8_unicode_ci;

create table users
(
  id       int auto_increment
    primary key,
  email    varchar(255) not null,
  password varchar(255) not null,
  fullName varchar(255) not null,
  city     varchar(255) not null,
  address  varchar(255) not null,
  birthday datetime     not null,
  phone    varchar(255) not null,
  constraint UNIQ_1483A5E9E7927C74
  unique (email)
)
  collate = utf8_unicode_ci;

create table evaluations
(
  id          int auto_increment
    primary key,
  value       double   not null,
  comment     longtext not null,
  dateAdded   datetime not null,
  authorId    int      not null,
  recepientId int      not null,
  courceId    int      null,
  courseId    int      not null,
  constraint FK_3B72691D76C6E161
  foreign key (recepientId) references users (id),
  constraint FK_3B72691D92AE6BCB
  foreign key (courceId) references courses (id),
  constraint FK_3B72691DA196F9FD
  foreign key (authorId) references users (id)
)
  collate = utf8_unicode_ci;

create index IDX_3B72691D76C6E161
  on evaluations (recepientId);

create index IDX_3B72691D92AE6BCB
  on evaluations (courceId);

create index IDX_3B72691DA196F9FD
  on evaluations (authorId);

create table users_roles
(
  user_id int not null,
  role_id int not null,
  primary key (user_id, role_id),
  constraint FK_51498A8EA76ED395
  foreign key (user_id) references users (id),
  constraint FK_51498A8ED60322AC
  foreign key (role_id) references roles (id)
)
  collate = utf8_unicode_ci;

create index IDX_51498A8EA76ED395
  on users_roles (user_id);

create index IDX_51498A8ED60322AC
  on users_roles (role_id);


INSERT INTO `roles` VALUES (1,'ROLES_ADMIN'),(2,'ROLES_TEACHER'),(3,'ROLES_STUDENT'),(4,'ROLES_PARENT');