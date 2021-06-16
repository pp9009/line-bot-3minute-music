create table music_data(
    `id` int not null auto_increment primary key,
    `uri` VARCHAR(255),
    `artists` VARCHAR(255),
    `popularity` int(11),
    `duration_ms` int(11),
    `isrc` VARCHAR(255),
    `register_date` datetime,
    `update_date` datetime
    );

create table users(
    `userid` VARCHAR(255) primary key,
    `used_count` int(11),
    `status` VARCHAR(255),
    `register_date` datetime,
    `update_date` datetime
    );