# JobLens

給求職者的透視企業的放大鏡。

# 需求

- JDK 25，例如[Temurin](https://adoptium.net/temurin/releases)
- MariaDB

# 執行

## 執行前

建立 `src/main/resources/application.properties` 並寫入以下內容：

```
spring.application.name=joblens
spring.datasource.url=jdbc:mariadb://localhost:3306/joblens

# Database username
spring.datasource.username=root

# Database password
spring.datasource.password=your_password

# JPA settings
spring.jpa.hibernate.ddl-auto=update
spring.jpa.show-sql=true
```

## Mac/Linux

```powershell
./mvnw springboot:run
```

## Windows

```powershell
.\mvnw.cmd springboot:run
```
