create table music(
    `id` int not null auto_increment primary key,
    `uri` VARCHAR(255), index uri_index (uri),
    `artists` VARCHAR(255),
    `popularity` int(10),
    `duration_ms` int(10),
    `isrc` VARCHAR(255),
    `register_date` datetime
    );

create table users(
    `userid` VARCHAR(255) primary key,
    `used_count` int(10) default 0,
    `register_date` datetime,
    `update_date` datetime
    );
