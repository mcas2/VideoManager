tabla de usuarios
INSERT INTO users ('id', 'name', 'surname', 'email','password', 'role', 'created_at') VALUES ();
INSERT INTO videos ('id', 'user_id', 'title', 'description','url', 'status', 'created_at', 'updated_at') VALUES ();


INSERT INTO users ('id', 'name', 'surname', 'email','password', 'role', 'created_at') VALUES (1, 'Gema', 'Rodríguez','gema@ubuntu.com', 'gema', 'ROLE_ADMIN', CURRENT_TIMESTAMP);
INSERT INTO users VALUES (1, 'Gema', 'Rodríguez','gema@ubuntu.com', 'gema', 'ROLE_ADMIN', CURRENT_TIMESTAMP);

INSERT INTO videos ('id', 'user_id', 'title', 'description','url', 'status', 'created_at', 'updated_at') VALUES ();
INSERT INTO videos VALUES (1, 2, '¿Qué es un fullstack?', 'Descripción del vídeo', 'https://www.youtube.com/watch?v=xhmyHZUNQNU', 'important', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO videos VALUES (2, 2, 'Diferencia entre Backend y Frontend', 'Descripción del vídeo', 'https://www.youtube.com/watch?v=50RbVujPPGs', 'important', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
