create table tracks(
    `id` int not null auto_increment primary key,
    `uri` VARCHAR(255), index uri_index (uri),
    `artists` text,
    `popularity` int,
    `duration_ms` int,
    `isrc` VARCHAR(255),
    `register_date` datetime
    );

create table users(
    `userid` VARCHAR(255) primary key,
    `used_count` int default 0,
    `register_date` datetime,
    `update_date` datetime
    );
