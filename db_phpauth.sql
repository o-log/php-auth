create table olog_auth_user (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand8291 */
alter table olog_auth_user add column login varchar(255)  not null   default ""  /* rand203511 */
alter table olog_auth_user add column password_hash varchar(255)  not null   default ""  /* rand633334 */
create table olog_auth_permission (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand7854 */
alter table olog_auth_permission add column title varchar(255)  not null    /* rand330932 */
alter table olog_auth_permission add unique key UK_title_613857 (title)  /* rand581998 */
insert into olog_auth_permission (title) values ("PERMISSION_PHPAUTH_MANAGE_USERS") /* ajhsdgfasdf */
insert into olog_auth_permission (title) values ("PERMISSION_PHPAUTH_MANAGE_OPERATORS") /* ounryuviuybve */
insert into olog_auth_permission (title) values ("PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS") /* grvygdsfg */
create table olog_auth_operator (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand4041 */
alter table olog_auth_operator add column title varchar(255)  not null   default ""  /* rand4028 */
alter table olog_auth_operator add column user_id int  not null    /* rand225666 */
alter table olog_auth_operator add foreign key FK_user_id_348736 (user_id)  references olog_auth_user (id) /* rand736528 */
alter table olog_auth_operator add unique key uk_user_id_6572523 (user_id) /* rand9287346598 */
create table olog_auth_operatorpermission (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand4503 */
alter table olog_auth_operatorpermission add column operator_id int  not null    /* rand873964 */
alter table olog_auth_operatorpermission add foreign key FK_operator_id_431153 (operator_id)  references olog_auth_operator (id) /* rand387171 */
alter table olog_auth_operatorpermission add column permission_id int  not null    /* rand9423 */
alter table olog_auth_operatorpermission add foreign key FK_permission_id_884208 (permission_id)  references olog_auth_permission (id) /* rand920855 */
alter table olog_auth_operatorpermission add unique key uk_operator_permission_7863845 (operator_id, permission_id) /* rand87263458 */
alter table olog_auth_user add unique key UK_login_581400 (login)  /* rand824395 */
create table olog_auth_permissiontouser (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand1719 */
alter table olog_auth_permissiontouser add column user_id int  not null    /* rand691200 */
alter table olog_auth_permissiontouser add constraint FK_user_id_233533 foreign key (user_id)  references olog_auth_user (id) /* rand770402 */
alter table olog_auth_permissiontouser add column permission_id int  not null    /* rand636735 */
alter table olog_auth_permissiontouser add constraint FK_permission_id_909906 foreign key (permission_id)  references olog_auth_permission (id) /* rand873944 */
alter table olog_auth_permissiontouser add unique key uk_user_permission_5672434 (user_id, permission_id) /* 78682345 */
alter table olog_auth_user add column description text    /* rand71301231234 */
alter table olog_auth_operator add column description text    /* rand559602131231 */
alter table olog_auth_user change column login login varchar(255) default null collate utf8_bin /* rand203511weqwqwef21313213 */
alter table olog_auth_user change column password_hash password_hash varchar(255)  not null   default "" collate utf8_bin /* rand76133334eer34e */
create table olog_auth_group (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand8442 */
alter table olog_auth_group add column title varchar(255)  not null   default ""  /* rand71192 */
insert into olog_auth_permission (title) values ("PERMISSION_PHPAUTH_MANAGE_GROUPS") /* grvygdsfg */
create table olog_auth_usertogroup (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand7717 */
alter table olog_auth_usertogroup add column user_id int  not null    /* rand127154 */
alter table olog_auth_usertogroup add constraint FK_user_id_668530 foreign key (user_id)  references olog_auth_user (id) /* rand704331 */
alter table olog_auth_usertogroup add column group_id int  not null    /* rand684719 */
alter table olog_auth_usertogroup add constraint FK_group_id_650820 foreign key (group_id)  references olog_auth_group (id) /* rand249455 */
alter table olog_auth_user add column owner_user_id int    /* rand537871 */
alter table olog_auth_user add constraint FK_owner_user_id_901133 foreign key (owner_user_id)  references olog_auth_user (id) /* rand135905 */
alter table olog_auth_user add column owner_group_id int    /* rand789986 */
alter table olog_auth_user add constraint FK_owner_group_id_195473 foreign key (owner_group_id)  references olog_auth_group (id) /* rand165719 */
alter table olog_auth_group add column owner_user_id int    /* rand459461 */
alter table olog_auth_group add constraint FK_owner_user_id_344841 foreign key (owner_user_id)  references olog_auth_user (id) /* rand963831 */
alter table olog_auth_group add column owner_group_id int    /* rand862130 */
alter table olog_auth_group add constraint FK_owner_group_id_417825 foreign key (owner_group_id)  references olog_auth_group (id) /* rand796751 */
alter table olog_auth_user add column has_full_access tinyint  not null   default 0  /* rand472298 */
alter table olog_auth_user add column primary_group_id int    /* rand264171 */
alter table olog_auth_user add constraint FK_primary_group_id_577270 foreign key (primary_group_id)  references olog_auth_group (id) /* rand704504 */
alter table olog_auth_usertogroup add unique key UK_user_id_group_id_653824 (user_id, group_id) /* rand249455 */

