--
-- PostgreSQL database dump
--

-- Dumped from database version 11.2
-- Dumped by pg_dump version 11.2

-- Started on 2019-03-21 15:28:43

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 199 (class 1259 OID 16410)
-- Name: addr_city; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.addr_city (
    id integer NOT NULL,
    name character varying(200),
    zip_code character varying(200),
    district integer NOT NULL
);


ALTER TABLE public.addr_city OWNER TO postgres;

--
-- TOC entry 196 (class 1259 OID 16395)
-- Name: addr_country; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.addr_country (
    name character varying(200),
    id integer NOT NULL,
    short_name character(2)
);


-- ALTER TABLE public.addr_country OWNER TO root;

--
-- TOC entry 198 (class 1259 OID 16405)
-- Name: addr_district; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.addr_district (
    id integer NOT NULL,
    name character varying(200),
    state integer NOT NULL
);


ALTER TABLE public.addr_district OWNER TO postgres;

--
-- TOC entry 197 (class 1259 OID 16400)
-- Name: addr_state; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.addr_state (
    id integer NOT NULL,
    name character varying(200),
    country integer
);


-- ALTER TABLE public.addr_state OWNER TO root;

--
-- TOC entry 201 (class 1259 OID 16438)
-- Name: addr_street; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.addr_street (
    id integer NOT NULL,
    name character varying(200)
);


ALTER TABLE public.addr_street OWNER TO postgres;

--
-- TOC entry 200 (class 1259 OID 16433)
-- Name: address; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.address (
    id integer NOT NULL,
    country integer NOT NULL,
    city integer NOT NULL,
    street integer NOT NULL,
    house_number character varying(10)
);


ALTER TABLE public.address OWNER TO postgres;

--
-- TOC entry 2841 (class 0 OID 16410)
-- Dependencies: 199
-- Data for Name: addr_city; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.addr_city (id, name, zip_code, district) FROM stdin;
\.


--
-- TOC entry 2838 (class 0 OID 16395)
-- Dependencies: 196
-- Data for Name: addr_country; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.addr_country (name, id, short_name) FROM stdin;
\.


--
-- TOC entry 2840 (class 0 OID 16405)
-- Dependencies: 198
-- Data for Name: addr_district; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.addr_district (id, name, state) FROM stdin;
\.


--
-- TOC entry 2839 (class 0 OID 16400)
-- Dependencies: 197
-- Data for Name: addr_state; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.addr_state (id, name, country) FROM stdin;
\.


--
-- TOC entry 2843 (class 0 OID 16438)
-- Dependencies: 201
-- Data for Name: addr_street; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.addr_street (id, name) FROM stdin;
\.


--
-- TOC entry 2842 (class 0 OID 16433)
-- Dependencies: 200
-- Data for Name: address; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.address (id, country, city, street, house_number) FROM stdin;
\.


--
-- TOC entry 2710 (class 2606 OID 16414)
-- Name: addr_city addr_city_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addr_city
    ADD CONSTRAINT addr_city_pkey PRIMARY KEY (id);


--
-- TOC entry 2704 (class 2606 OID 16399)
-- Name: addr_country addr_country_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.addr_country
    ADD CONSTRAINT addr_country_pkey PRIMARY KEY (id);


--
-- TOC entry 2708 (class 2606 OID 16409)
-- Name: addr_district addr_district_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addr_district
    ADD CONSTRAINT addr_district_pkey PRIMARY KEY (id);


--
-- TOC entry 2706 (class 2606 OID 16404)
-- Name: addr_state addr_state_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.addr_state
    ADD CONSTRAINT addr_state_pkey PRIMARY KEY (id);


--
-- TOC entry 2714 (class 2606 OID 16444)
-- Name: addr_street addr_street_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addr_street
    ADD CONSTRAINT addr_street_name_key UNIQUE (name);


--
-- TOC entry 2716 (class 2606 OID 16442)
-- Name: addr_street addr_street_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addr_street
    ADD CONSTRAINT addr_street_pkey PRIMARY KEY (id);


--
-- TOC entry 2712 (class 2606 OID 16437)
-- Name: address address_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.address
    ADD CONSTRAINT address_pkey PRIMARY KEY (id);


-- Completed on 2019-03-21 15:28:44

--
-- PostgreSQL database dump complete
--

