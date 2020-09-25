SET sql_mode = '';

create table users (
id int not null auto_increment primary key unique ,
name varchar(191) not null ,
email varchar(255) not null unique ,
password varchar(191) ,
created_at timestamp ,
updated_at timestamp
);

create table boards (
id integer not null auto_increment primary key unique ,
title varchar(191) not null,
ordering integer not null,
user_id integer not null,
foreign key (user_id) references users(id) on delete cascade ,
created_at timestamp,
updated_at timestamp
);

create table lists (
id integer not null auto_increment primary key unique ,
title varchar(191) not null,
ordering integer not null,
board_id integer not null ,
foreign key (board_id) references boards(id) on delete cascade,
created_at timestamp,
updated_at timestamp
);

create table cards (
id integer not null auto_increment primary key unique ,
description text not null,
ordering integer not null,
list_id integer not null ,
foreign key (list_id) references lists(id) on delete cascade,
created_at timestamp,
updated_at timestamp
);

create table activity (
id integer not null auto_increment primary key unique ,
text text not null,
ordering integer not null,
card_id integer not null ,
foreign key (card_id) references cards(id) on delete cascade,
created_at timestamp,
updated_at timestamp
);
