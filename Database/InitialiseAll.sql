﻿DROP TABLE drives;
DROP TABLE owns;
DROP TABLE bids;
DROP TABLE manages;
DROP TABLE users;
DROP TABLE rides;
DROP TABLE cars;
DROP TABLE admins;

-- Users
CREATE TABLE users (
    email VARCHAR(32) unique,
    username VARCHAR(32) unique not null,
    pwd VARCHAR(32) not null,
    phone numeric(8, 0) not null,
    gender VARCHAR(6),
    bday DATE,
    driverLicense VARCHAR(9),
    primary key(email),
    check(email like '___%@%___.com' and username not like '%[0-9]%')
);

-- Rides
CREATE TABLE rides (
    rideid VARCHAR(100) unique,
    dates DATE,
    times TIME,
    origin VARCHAR(32) not null,
    destination VARCHAR(32) not null,
    basePrice INTEGER not null,
    capacity INTEGER not null,
    sidenote TEXT,
    primary key(rideid, dates, times)
);

-- Cars
CREATE TABLE cars (
    carid VARCHAR(100) unique,
    licensePlate VARCHAR(8),
    carType VARCHAR(32) not null,
    primary key(carid, licensePlate)
);

-- Admins
CREATE TABLE admins (
    adminid VARCHAR(100) unique,
    adminname VARCHAR(32) unique,
    adminpwd VARCHAR(32) not null,
    employeename VARCHAR(32) not null,
    primary key(adminid, adminname)
);

-- User owns cars
CREATE TABLE owns (
    emails VARCHAR(32),
    carsid VARCHAR(100),
    primary key(emails, carsid),
    foreign key(emails) references users(email)
        on DELETE CASCADE,
    foreign key(carsid) references cars(carid)
        on DELETE CASCADE
);

-- User drives rides
CREATE TABLE drives (
    email VARCHAR(32),
    ridesid VARCHAR(100),
    carid VARCHAR(100) not null,
    datess DATE not null,
    timess TIME not null,
    primary key(email, datess, timess),
    foreign key(email, carid) references owns(emails, carsid)
        on DELETE CASCADE,
    foreign key(ridesid, datess, timess) references rides(rideid, dates, times)
        on DELETE CASCADE
);

-- User bids for rides
CREATE TABLE bids (
    emails VARCHAR(32),
    ridesid VARCHAR(100),
    price INTEGER not null,
    status INTEGER not null,
    sidenote TEXT,
    primary key(emails, ridesid),
    foreign key(emails) references users(email)
        on DELETE CASCADE,
    foreign key(ridesid) references rides(rideid)
        on DELETE CASCADE
);

-- Administrator manages entities
CREATE TABLE manages (
    adminsid VARCHAR(100) not null,
    managetype VARCHAR(6) not null,
    typeid VARCHAR(100) not null,
    history VARCHAR(32) not null,
    foreign key(adminsid) references admins(adminid)
        on DELETE CASCADE
);

-- Users
INSERT INTO users
values('renee@gmail.com', 'Renee', 123456, 81234567, 'Female', '1997-06-01', 'S1234567A');
INSERT INTO users
values('yilun@gmail.com', 'Yilun', 234567, 81234569, 'Male', '1997-06-02', 'S1234567B');
INSERT INTO users
values('brian@gmail.com', 'Brian', 345678, 81234561, 'Male', '1997-06-03', 'S1234567C');
INSERT INTO users
values('cindy@gmail.com', 'Cindy', 456789, 81234562, 'Female', '1997-06-04', 'S1234567D');
INSERT INTO users
values('billy@gmail.com', 'Billy', 567891, 81234563, 'Male', '1997-06-05', null);
INSERT INTO users
values('peggy@gmail.com', 'Peggy', 678912, 81234564, 'Female', null, null);
INSERT INTO users
values('alex@gmail.com', 'Alex', 789123, 81234565, null, null, null);
INSERT INTO users
values('anna@gmail.com', 'Anna', 891234, 81234566, 'Female', null, 'S1234567E');
INSERT INTO users
values('martin@gmail.com', 'Martin', 912345, 81234568, null, '1997-06-06', 'S1234567F');

-- Rides
INSERT INTO rides
values(12345678, '2018-02-28', '14:20:20', 'Tampines MRT', 'Changi Airport', 10, 3, 'Only waiting for 10 minutes');
INSERT INTO rides
values(23456781, '2018-02-28', '15:20:20', 'Tampines MRT', 'Bishan MRT', 30, 2, null);
INSERT INTO rides
values(34567812, '2018-02-28', '09:00:20', 'NUS School of Computing', 'Bishan MRT', 25, 2, null);
INSERT INTO rides
values(45678123, '2018-02-28', '09:05:20', 'Botanic Gardens MRT', 'Bishan MRT', 15, 5, null);
INSERT INTO rides
values(56781234, '2018-03-01', '09:30:00', 'Clementi MRT', 'Ang Mo Kio MRT', 24, 3, 'Waiting for 5 minutes');
INSERT INTO rides
values(67812345, '2018-03-03', '22:05:08', 'Crescent Girls School', 'Orchard MRT', 10, 5, null);

-- Cars
INSERT INTO cars
values(1234567, 'SGD1234A', 'Black SUV');
INSERT INTO cars
values(2345671, 'SGD1234B', 'White Honda');
INSERT INTO cars
values(3456712, 'SGD1234C', 'Blue Audi');
INSERT INTO cars
values(4567123, 'SGD1234D', 'Silver Toyota');
INSERT INTO cars
values(5671234, 'SGD1234E', 'Red Lexus');
INSERT INTO cars
values(6712345, 'SGD1234F', 'Dark Blue Ford');
INSERT INTO cars
values(7123456, 'SGD1234G', 'White Nissan');

-- Admins
INSERT INTO admins
values(1234, 'admin1', 2280, 'Renee');
INSERT INTO admins
values(2341, 'admin2', 1198, 'Yilun');
INSERT INTO admins
values(3412, 'admin3', 8739, 'Brian');
INSERT INTO admins
values(4123, 'admin4', 9932, 'Cindy');

-- User owns cars
INSERT INTO owns
values('renee@gmail.com', 1234567);
INSERT INTO owns
values('renee@gmail.com', 7123456);
INSERT INTO owns
values('yilun@gmail.com', 2345671);
INSERT INTO owns
values('brian@gmail.com', 3456712);
INSERT INTO owns
values('cindy@gmail.com', 4567123);
INSERT INTO owns
values('anna@gmail.com', 5671234);
INSERT INTO owns
values('martin@gmail.com', 6712345);

-- User drives rides
INSERT INTO drives
values('renee@gmail.com', 12345678, 1234567, '2018-02-28', '14:20:20');
INSERT INTO drives
values('renee@gmail.com', 23456781, 1234567, '2018-02-28', '15:20:20');
INSERT INTO drives
values('yilun@gmail.com', 34567812, 2345671, '2018-02-28', '09:00:20');
INSERT INTO drives
values('anna@gmail.com', 45678123, 5671234, '2018-02-28', '09:05:20');
INSERT INTO drives
values('martin@gmail.com', 56781234, 6712345, '2018-03-01', '09:30:00');
INSERT INTO drives
values('cindy@gmail.com', 67812345, 4567123, '2018-03-03', '22:05:08');

-- User bids for rides
INSERT INTO bids
values('billy@gmail.com', 12345678, 12, 0, null);
INSERT INTO bids
values('alex@gmail.com', 12345678, 13, 0, null);
INSERT INTO bids
values('martin@gmail.com', 56781234, 24, 0, null);
INSERT INTO bids
values('anna@gmail.com', 67812345, 15, 0, null);
INSERT INTO bids
values('brian@gmail.com', 34567812, 26, 0, null);
INSERT INTO bids
values('alex@gmail.com', 45678123, 15, 0, null);

-- Administrator manages entities
INSERT INTO manages
values(1234, 'Rides', '34567812', 'Modify comments');
