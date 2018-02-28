-- Users
CREATE TABLE users (
    email VARCHAR(32),
    userid INTEGER,
    username VARCHAR(32) unique not null,
    pwd VARCHAR(32) not null,
    phone numeric(8, 0) not null,
    gender VARCHAR(6),
    bday DATE,
    driverLicense VARCHAR(9),
    primary key(email, userid),
    check(email like '___%@%___.com' and username not like '%[0-9]%')
);

-- Rides
CREATE TABLE rides (
    rideid INTEGER unique primary key,
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
    carid INTEGER unique,
    licensePlate VARCHAR(8),
    carType VARCHAR(32) not null,
    primary key(carid, licensePlate)
);

-- Admins
CREATE TABLE admins (
    adminid INTEGER unique,
    adminname VARCHAR(32) unique,
    adminpwd VARCHAR(32) not null,
    employeename VARCHAR(32) not null,
    primary key(adminid, adminname)
);

-- User owns cars
CREATE TABLE owns (
    usersid INTEGER,
    carsid INTEGER,
    primary key(usersid, carsid),
    foreign key(usersid) references users(userid)
        on DELETE CASCADE,
    foreign key(carsid) references cars(carid)
        on DELETE CASCADE
);

-- User drives rides
CREATE TABLE drives (
    usersid INTEGER,
    ridesid INTEGER unique not null,
    carsid INTEGER not null,
    primary key(usersid, ridesid),
    foreign key(usersid) references users(userid)
        on DELETE CASCADE,
    foreign key(ridesid) references rides(rideid)
        on DELETE CASCADE,
    foreign key(carsid) references cars(carid)
        on DELETE CASCADE
);

-- User bids for rides
CREATE TABLE bids (
    usersid INTEGER,
    ridesid INTEGER,
    price INTEGER not null,
    status VARCHAR(1) not null,
    sidenote TEXT,
    primary key(usersid, ridesid),
    foreign key(usersid) references users(userid)
        on DELETE CASCADE,
    foreign key(ridesid) references rides(rideid)
        on DELETE CASCADE
);

-- Administrator manages entities
CREATE TABLE manages (
    adminsid INTEGER not null,
    managetype VARCHAR(6) not null,
    typeid INTEGER not null,
    history VARCHAR(32) not null,
    foreign key(adminsid) references admins(adminid)
        on DELETE CASCADE
);