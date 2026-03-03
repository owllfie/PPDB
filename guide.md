table jurusan:
id_jurusan int(11) pk,
nama_jurusan varchar(50)

table registrasi:
id_registrasi int(11) pk,
nisn varchar(50),
nama_lengkap varchar(50),
nik varchar(50),
tempat_lahir varchar(50),
tanggal_lahir date,
jenis_kelamin varchar(50),
agama varchar(50),
anak_ke- int(11),
alamat_lengkap varchar(255),
nama_ayah varchar(50),
nama_ibu varchar(50),
pekerjaan_ayah varchar(50),
pekerjaan_ibu varchar(50),
created_at timestamp
kk varchar(255),
ijazah varchar(255),
akta_lahir varchar(255),
updated_at timestamp,
sekolah_asal varchar(50),
nilai_rapor int(11),
no_hp varchar(50),
email varchar(255)

table users:
id_user int(11) pk,
username varchar(50),
email varchar(255),
password varchar(50),
created_at timestamp,
role int(11) fk

table role:
id_role int(11) pk,
role varchar(50)

table activity_log:
id_log int(11) pk,
id_user int(11) fk,
action varchar(255),
ip_address varchar(50),
created_at timestamp

table password_reset_token:
id int(11) pk,
email varchar(255),
phone varchar(50),
token varchar(255),
otp varchar(255),
created_at timestamp

table setting:
id_setting int(11) pk,
nama_sekolah varchar(50),
logo_sekolah varchar(255),
alamat varchar(255),
admin varchar(50),
nomor_kontak varchar(50),
created_at timestamp,
created_by int(11),
updated_at timestamp,
updated_by int(11)