# JobLens

給求職者的透視企業的放大鏡。

# 需求

PHP, Apache2, MySQL

你也可以使用Xampp。

# 執行

git clone至Apache2的htdocs。

在 php.ini (可從xampp Apache Config找) uncomment `extension=intl;`

若你在Ubuntu LTS 20.04上，安裝`php7.4-intl`:

```
sudo apt install php7.4-intl
```

在資料庫管理系統新增帳密皆為`joblens`的使用者，並需擁有SELECT權限。

> [!NOTE]
> 若出現password policy錯誤，請執行以下SQL指令:
> `SET GLOBAL validate_password.policy = LOW;`


在`my.ini`新增以下設置。注意MySQL和MariaDB設置不同。須在XAMPP重啟MySQL以啟用變更:

## MySQL

```
[mysqld]
ngram_token_size=2
```

## MariaDB

```
[mysqld]
innodb_ft_min_token_size=1
```

在瀏覽器網址列輸入 `localhost`。