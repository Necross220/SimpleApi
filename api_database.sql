-- auto-generated definition
create schema api collate latin1_swedish_ci;

use api;

create table if not exists status
(
	id int auto_increment
		primary key,
	name varchar(30) not null,
	active bit default b'1' null,
	created_at datetime default CURRENT_TIMESTAMP null
);

create table if not exists contacts
(
	id int auto_increment
		primary key,
	name varchar(30) null,
	lastname varchar(30) null,
	email varchar(60) null,
	modified_at datetime default CURRENT_TIMESTAMP null,
	created_at datetime default CURRENT_TIMESTAMP null,
	status_id int default 1 null,
	constraint contacts_status_id_fk
		foreign key (status_id) references status (id)
);

create table if not exists contact_phones
(
	id int auto_increment
		primary key,
	contact_id int null,
	number int null,
	created_at datetime default CURRENT_TIMESTAMP null,
	modified_at datetime default CURRENT_TIMESTAMP null,
	status_id int default 1 null,
	constraint contact_phones_contacts_id_fk
		foreign key (contact_id) references contacts (id),
	constraint contact_phones_status_id_fk
		foreign key (status_id) references status (id)
);

create procedure CreateContacts(IN _name varchar(30), IN _lastname varchar(30), IN _email varchar(60))
BEGIN
    INSERT INTO contacts (name, lastname, email) VALUES (_name, _lastname, _email);
    SELECT LAST_INSERT_ID() AS last_id;
END;

create procedure CreatePhones(IN _contact_id varchar(30), IN _number varchar(30))
BEGIN
    INSERT INTO contact_phones (contact_id, number) VALUES (_contact_id, _number);
END;

create procedure DeleteContacts(IN _id int)
BEGIN
    UPDATE contacts
    SET status_id = 2
    WHERE id = _id;
END;

create procedure ReadContacts(IN _Id int)
BEGIN
    SELECT c.*
    FROM contacts c
             JOIN status s on c.status_id = s.id
    WHERE (c.id = _Id OR 0 = _Id)
      AND c.status_id = 1;
END;

create procedure ReadPhones(IN _Id int)
BEGIN
    SELECT number
    FROM contact_phones cp
    WHERE (cp.contact_id = _Id OR 0 = _Id);
END;

create procedure UpdateContacts(IN _id int, IN _name varchar(30), IN _lastname varchar(30), IN _email varchar(60))
BEGIN
    UPDATE contacts
    SET name        = _name,
        lastname    = _lastname,
        email       =_email,
        modified_at = CURRENT_TIMESTAMP
    WHERE id = _id;
END;