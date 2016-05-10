array(
'create table olog_auth_user (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand8291 */;',
'alter table olog_auth_user add column login varchar(255)  not null   default ""  /* rand203511 */;',
'alter table olog_auth_user add column password_hash varchar(255)  not null   default ""  /* rand633334 */;',
'create table olog_auth_permission (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand7854 */;',
'alter table olog_auth_permission add column title varchar(255)  not null    /* rand330932 */;',
'alter table olog_auth_permission add unique key UK_title_613857 (title)  /* rand581998 */;',
'insert into olog_auth_permission (title) values ("PERMISSION_PHPAUTH_MANAGE_USERS") /* ajhsdgfasdf */;',
'insert into olog_auth_permission (title) values ("PERMISSION_PHPAUTH_MANAGE_OPERATORS") /* ounryuviuybve */;',
'create table olog_auth_operator (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand4041 */;',
)
