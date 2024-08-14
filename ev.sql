-- Table: public.feedback

-- DROP TABLE IF EXISTS public.feedback;

CREATE TABLE IF NOT EXISTS public.feedback
(
    id integer NOT NULL DEFAULT nextval('feedback_id_seq'::regclass),
    rating integer NOT NULL,
    feedback text COLLATE pg_catalog."default",
    station_id integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT feedback_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.feedback
    OWNER to postgres;

    -- Table: public.machines

-- DROP TABLE IF EXISTS public.machines;

CREATE TABLE IF NOT EXISTS public.machines
(
    id integer NOT NULL,
    station_id integer,
    charger_type character varying(255) COLLATE pg_catalog."default",
    total_number_available integer,
    queue integer,
    CONSTRAINT machines_pkey PRIMARY KEY (id),
    CONSTRAINT machines_station_id_fkey FOREIGN KEY (station_id)
        REFERENCES public.station (station_id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.machines
    OWNER to postgres;-- Table: public.operator

-- DROP TABLE IF EXISTS public.operator;

CREATE TABLE IF NOT EXISTS public.operator
(
    id integer NOT NULL,
    contact bigint,
    name character varying(255) COLLATE pg_catalog."default",
    age integer,
    email character varying(30) COLLATE pg_catalog."default",
    CONSTRAINT operator_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.operator
    OWNER to postgres;-- Table: public.profile

-- DROP TABLE IF EXISTS public.profile;

CREATE TABLE IF NOT EXISTS public.profile
(
    name character varying(100) COLLATE pg_catalog."default" NOT NULL,
    phone_number character varying(20) COLLATE pg_catalog."default" NOT NULL,
    car_model character varying(100) COLLATE pg_catalog."default" NOT NULL,
    car_number character varying(20) COLLATE pg_catalog."default" NOT NULL,
    charger_type character varying(50) COLLATE pg_catalog."default" NOT NULL,
    address character varying(255) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT profile_pkey PRIMARY KEY (phone_number)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.profile
    OWNER to postgres;-- Table: public.reg

-- DROP TABLE IF EXISTS public.reg;

CREATE TABLE IF NOT EXISTS public.reg
(
    user_name character varying(25) COLLATE pg_catalog."default",
    userid integer NOT NULL DEFAULT nextval('reg_userid_seq'::regclass),
    email character varying(100) COLLATE pg_catalog."default",
    mobile_no bigint NOT NULL,
    password character varying(255) COLLATE pg_catalog."default",
    usertype character varying(15) COLLATE pg_catalog."default",
    CONSTRAINT reg_pkey PRIMARY KEY (userid, mobile_no),
    CONSTRAINT reg_email_key UNIQUE (email)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.reg
    OWNER to postgres;-- Table: public.request

-- DROP TABLE IF EXISTS public.request;

CREATE TABLE IF NOT EXISTS public.request
(
    req_no serial primary key;
    column mobile_no bigint;
    request add column user_name varchar(25);
    ulat double precision,
    ulng double precision,
    station character varying(20) COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.request
    OWNER to postgres;-- Table: public.station

-- DROP TABLE IF EXISTS public.station;

CREATE TABLE IF NOT EXISTS public.station
(
    station_id integer NOT NULL,
    station_name character varying(50) COLLATE pg_catalog."default",
    station_status character varying(20) COLLATE pg_catalog."default",
    longitude double precision,
    latitude double precision,
    operator_id integer,
    CONSTRAINT station_pkey PRIMARY KEY (station_id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.station
    OWNER to postgres;

-- Default initial values --

insert into machines(id,station_id,charge_type,total_number_available,queue)
values(4,2,'Level 1',3,6),(5,2,'Level 2',5,4),(6,2,'DC Fast Charger',3,2),
(7,3,'Level 1',7,4),(8,3,'Level 2',5,3),(9,3,'DC Fast Charger',4,5),
(10,4,'Level 1',4,3),(11,4,'Level 2',2,3),(12,4,'DC Fast Charger',4,4),
(13,5,'Level 1',7,5),(14,5,'Level 2',5,1),(15,5,'DC Fast Charger',5,2),
(3,1,'DC Fast Charger',4,6),(1,1,'Level 1',5,3),(2,1,'Level 2',4,2);

insert into operator values(1,7700000001,'Operator1',34,'o1@gmail.com'),
(2,7700000002,'Operator2',30,'o2@gmail.com'),(3,7700000003,'Operator3',31,'o3@gmail.com'),
(4,7700000004,'Operator4',39,'o4@gmail.com'),(5,7700000005,'Operator5',34,'o5@gmail.com');

insert into reg(usertype,mobile_no,user_name,password,email) 
values('operator',7700000001,'Operator1','181','o1@gmail.com'),
('operator',7700000002,'Operator2','182','o2@gmail.com'),
('operator',7700000003,'Operator3','183','o3@gmail.com'),
('operator',7700000004,'Operator4','184','o4@gmail.com'),
('operator',7700000005,'Operator5','185','o5@gmail.com');

INSERT INTO station (station_id, station_name, station_status, longitude, latitude, operator_id) VALUES
(1, 'Adyar', 'open', 80.2572, 13.0064, 1),
(2, 'T.Nagar', 'open', 80.2344, 13.0329, 2),
(3, 'Mylapore', 'open', 80.2707, 13.0339, 3),
(4, 'Guindy', 'open', 80.2209, 13.0067, 4),
(5, 'Anna Nagar', 'open', 80.2088, 13.0878, 5);
