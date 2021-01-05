[toc]
#Mbatis全局配置文件
##mybatis配置文件
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE configuration
  PUBLIC "-//mybatis.org//DTD Config 3.0//EN"
  "http://mybatis.org/dtd/mybatis-3-config.dtd">
<configuration>
  <properties resource="conf/mybatis.properties"></properties>
  <environments default="development">
    <environment id="development">
      <transactionManager type="JDBC"/>
      <dataSource type="POOLED">
        <property name="driver" value="${mysql.driver}"/>
        <property name="url" value="${mysql.url}"/>
        <property name="username" value="${mysql.user}"/>
        <property name="password" value="${mysql.password}"/>
      </dataSource>
    </environment>
  </environments>
  <mappers>
    <mapper resource="mapper/companyarea.xml"/>
    <mapper resource="mapper/dept.xml"/>
  </mappers>
</configuration>
```
##1.properties标签

>属性
>>resource:引用类路径下的配置文件
>>url:引入网络路径下的配置文件

>功能
>>主要用于配置datasource数据源的相关信息

```
<properties resource="conf/mybatis.properties"></properties>
```

##2.settings标签
文档位置 https://mybatis.org/mybatis-3/zh/configuration.html#settings
一个配置完整的 settings 元素的示例如下
```xml
<settings>
  <setting name="cacheEnabled" value="true"/>
  <setting name="lazyLoadingEnabled" value="true"/>
  <setting name="multipleResultSetsEnabled" value="true"/>
  <setting name="useColumnLabel" value="true"/>
  <setting name="useGeneratedKeys" value="false"/>
  <setting name="autoMappingBehavior" value="PARTIAL"/>
  <setting name="autoMappingUnknownColumnBehavior" value="WARNING"/>
  <setting name="defaultExecutorType" value="SIMPLE"/>
  <setting name="defaultStatementTimeout" value="25"/>
  <setting name="defaultFetchSize" value="100"/>
  <setting name="safeRowBoundsEnabled" value="false"/>
  <!--开启驼峰命名法-->
  <setting name="mapUnderscoreToCamelCase" value="true"/>
  <setting name="localCacheScope" value="SESSION"/>
  <setting name="jdbcTypeForNull" value="OTHER"/>
  <setting name="lazyLoadTriggerMethods" value="equals,clone,hashCode,toString"/>
</settings>
```

##3.typeAliases标签

>功能
>>别名处理器，用于给常用的java全类名起别名，在sql映射文件使用全类名时可以使用别名，比如resultType取值可以写成定义好的别名

```xml
<typeAliases>
  <typeAlias alias="Author" type="domain.blog.Author"/>
  <typeAlias alias="Blog" type="domain.blog.Blog"/>
  <typeAlias alias="Comment" type="domain.blog.Comment"/>
  <typeAlias alias="Post" type="domain.blog.Post"/>
  <typeAlias alias="Section" type="domain.blog.Section"/>
  <typeAlias alias="Tag" type="domain.blog.Tag"/>
</typeAliases>
```
也可以指定包名
```xml
<typeAliases>
  <package name="domain.blog"/>
</typeAliases>
```
在包下的每个javaBean，在没有注解的情况下，会使用 Bean 的首字母小写的非限定类名来作为它的别名
<font>使用@Alias注解自定义别名</font>
```java
@Alias("author")
public class Author {
    ...
}
```

##4.typeHandlers标签

>功能
>>类型处理器，用于java和数据库之间数据转换的桥梁

##5.plugins标签
>功能
>>MyBatis 允许你在映射语句执行过程中的某一点进行拦截调用

+ Executor (update, query, flushStatements, commit, rollback, getTransaction, close, isClosed)
+ ParameterHandler (getParameterObject, setParameters)
+ ResultSetHandler (handleResultSets, handleOutputParameters)
+ StatementHandler (prepare, parameterize, batch, update, query)

##6.environments标签
>功能
>>配置数据库

```xml
<!--
	default : 默认环境id ， 对应里面environment标签的id值
-->
<environments default="development">
  <!--
  environment标签
  	id ： 当前环境唯一标识
  -->
  <environment id="test">
  	<!--
      transactionManager
        type ： 事务的提交方式
        整合了spring后使用spring的事务提交方式，可以不配置
      -->
    <transactionManager type="JDBC">
      <property name="..." value="..."/>
    </transactionManager>
    <!--
      dataSource
        type ： 数据源类型	也就是 type="[UNPOOLED|POOLED|JNDI]"）
        	UNPOOLED– 这个数据源的实现会每次请求时打开和关闭连接。
            POOLED– 这种数据源的实现利用“池”的概念将 JDBC 连接对象组织起来，避免了创建新的连接实例时所必需的初始化和认证时间。
      -->
    <dataSource type="POOLED">
      <property name="driver" value="${driver}"/>
      <property name="url" value="${url}"/>
      <property name="username" value="${username}"/>
      <property name="password" value="${password}"/>
    </dataSource>
  </environment>

  <environment id="development">
    <transactionManager type="JDBC">
      <property name="..." value="..."/>
    </transactionManager>
    <dataSource type="POOLED">
      <property name="driver" value="${driver}"/>
      <property name="url" value="${url}"/>
      <property name="username" value="${username}"/>
      <property name="password" value="${password}"/>
    </dataSource>
  </environment>
</environments>
```

##7.databaseIdProvider标签
>功能
>>多数据库支持,得到数据库厂商的表示，mybatis根据不同的标识来执行不同sql

```xml
<databaseIdProvider type="DB_VENDOR">
  <!--为不同的数据库厂商起别名-->
  <property name="Mysql" value="mysql"/>
  <property name="SQL Server" value="sqlserver"/>
  <property name="DB2" value="db2"/>
  <property name="Oracle" value="oracle" />
</databaseIdProvider>
```
上面定义的数据库厂商别名在映射文件中使用
```xml
<!--
databaseId属性用来指定数据库厂商别名
-->
<select id="selectById" resultType="mybatis.model.Dept" databaseId="mysql">
    select * from dept where id=#{id}
</select>
```

##8.mappers标签
引用映射文件
```xml
<mappers>
  <!--单个文件引入-->
  <mapper resource="org/mybatis/builder/AuthorMapper.xml"/>
  
  <!--
  	注意： class导入和批量导入需要接口文件和映射文件处于同一目录下
  -->
  <mapper class="接口文件的全类名"/>
  <!--批量引入-->
  <package name="org.mybatis.builder"/>
</mappers>
```