-- Users
CREATE TABLE users (
    email VARCHAR(32) primary key,
    userid INTEGER primary key,
    username VARCHAR(32) unique not null,
    pwd VARCHAR(32) not null,
    phone numeric(8, 0) not null,
    gender VARCHAR(6),
    bday DATE,
    driverLicense VARCHAR(9),
    check(email like '___%@%___.com' and username not like '%[0-9]%')
);

-- Rides
CREATE TABLE rides (
    rideid INTEGER primary key,
    dates DATE not null,
    times TIME not null,
    origin VARCHAR(32) not null,
    destination VARCHAR(32) not null,
    basePrice INTEGER not null,
    capacity INTEGER not null,
    biddingType VARCHAR(10) not null,
    sidenote TEXT
);

-- Cars
CREATE TABLE cars (
    carid INTEGER primary key,
    licensePlate VARCHAR(8) primary key,
    carType VARCHAR(32) not null,
    check(licensePlate like '[A-Z][A-Z][A-Z][0-9][0-9][0-9][0-9][A-Z]')
);

-- Admins
CREATE TABLE admins (
    adminid INTEGER primary key,
    adminname VARCHAR(32) primary key,
    adminpwd VARCHAR(32) not null,
    employeename VARCHAR(32) not null
);

-- User owns cars
CREATE TABLE owns (
    userid INTEGER primary key,
    carid INTEGER primary key
);

-- User drives rides
CREATE TABLE drives (
    userid INTEGER primary key,
    dates DATE primary key,
    times TIME primary key,
    rideid INTEGER unique not null,
    carid INTEGER not null
);

-- User bids for rides
CREATE TABLE bids (
    userid INTEGER primary key,
    rideid INTEGER primary key,
    price INTEGER not null,
    status VARCHAR(1) not null,
    sidenote TEXT
);

-- Administrator manages entities
CREATE TABLE manages (
    adminid INTEGER not null,
    managetype VARCHAR(6) not null,
    typeid INTEGER not null,
    history VARCHAR(32) not null
);