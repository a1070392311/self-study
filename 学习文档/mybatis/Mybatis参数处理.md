#Mybatis参数处理
[toc]
##原始类型或简单数据类型
原始类型或简单数据类型,数据不会被特殊处理,（比如 Integer 和 String）
```xml
<select id="selectUsers" parameterType="Integer" resultType="User">
  select id, username, password
  from users
  where id = #{id}
</select>
```
##model对象类型参数
对应属性值传参
```xml
<insert id="insertUser" parameterType="User">
  insert into users (id, username, password)
  values (#{id}, #{username}, #{password})
</insert>
```
参数也可以指定一个特殊的数据类型,例如
```xml
#{property,javaType=int,jdbcType=NUMERIC}
```

>参数可使用的属性
>>javaType: 参数对象的类型
>>>除非该对象是一个 HashMap。这个时候，你需要显式指定 javaType 来确保正确的类型处理器（TypeHandler）被使用

>>jdbcType: JDBC 类型
>>typeHandler: 特殊的类型处理器类（或别名）
>>numericScale:指定小数点后保留的位数。

###多个参数 
任意多个参数，都会被MyBatis重新包装成一个Map传入。 Map的key是param1，param2，0，1…，值就是参数的值。 

###命名参数 
为参数使用@Param起一个名字，MyBatis就会将这些参数封 装进map中，key就是我们自己指定的名字 

### POJO 
当这些参数属于我们业务POJO时，我们直接传递POJO 

###Map 
我们也可以封装多个参数为map，直接传递


###{}和${}的区别
>\#{}安全，转义，编译执行
>${}不安全，不转义，但是能动态添加，比如 ORDER BY 子句

###多参数传值
>比如
```java
//接口中定义以下方法
public Long selectById(String name , Integer id);
```

><font color="#f00" size=3>在映射文件中直接调用是错误的</font>
```xml
//错误用法
<select id="selectById" resultType="mybatis.model.Dept">
    select * from dept where id=#{id} and name=#{name}
</select>
```

><font color="green" size=3>正确的映射写法</font>
>任意多个参数，都会被MyBatis重新包装成一个Map传入。 Map的key是param1，param2，0，1…，值就是参数的值
```xml
//第一种解决方法
<select id="selectById" resultType="mybatis.model.Dept">
    select * from dept where id=#{param2} and name=#{param1}
</select>
```
>但是第一种写法在不适用超长sql或者参数超多的sql
><font color="green" size=3>第二种解决方案</font>
```java
//使用@Param注解规定变量名
public Dept selectByParam(@Param("name")String name , @Param("id")Integer id);
```

###resultType和resultMap
>####resultType
>规定返回期望类型的全类名或别名，如果是list集合，则写list集合子元素的类型。如果是map。直接写map，该属性和resultMap不能同时使用

>####resultMap
>自定义返回类型

```xml
<!--
resultMap标签，自定义返回类型
	type：自定义规则的javaBean类型
    id:唯一id方便引用
-->
<resultMap type="map" id="mymap">
	<!--
    	id标签定义主键会底层有优化；
        column：指定哪一列
        property：指定对应的javaBean属性
    -->
    <id column="id" property="id"/>
    <!-- 定义普通列封装规则 -->
	<result column="name" property="name"/>
    <!-- 其他不指定的列会自动封装：我们只要写resultMap就把全部的映射规则都写上。 -->
</resultMap>

<select id="selectReturnSMap" resultMap="mymap">
    select * from dept where id=#{id}
</select>
```

###resultMap 元素的概念视图
* constructor - 用于在实例化类时，注入结果到构造方法中
  + idArg - ID 参数；标记出作为 ID 的结果可以帮助提高整体性能
  + arg - 将被注入到构造方法的一个普通结果
* id – 一个 ID 结果；标记出作为 ID 的结果可以帮助提高整体性能
* result – 注入到字段或 JavaBean 属性的普通结果
* association – 一个复杂类型的关联；许多结果将包装成这种类型
  + 嵌套结果映射 – 关联可以是 resultMap 元素，或是对其它结果映射的引用
* collection – 一个复杂类型的集合
  + 嵌套结果映射 – 集合可以是 resultMap 元素，或是对其它结果映射的引用
* discriminator – 使用结果值来决定使用哪个 resultMap
  + case – 基于某些值的结果映射
    - 嵌套结果映射 – case 也是一个结果映射，因此具有相同的结构和元素；或者引用其它的结果映射

***
>案例

```xml
<!-- 非常复杂的语句 -->
<select id="selectBlogDetails" resultMap="detailedBlogResultMap">
  select
       B.id as blog_id,
       B.title as blog_title,
       B.author_id as blog_author_id,
       A.id as author_id,
       A.username as author_username,
       A.password as author_password,
       A.email as author_email,
       A.bio as author_bio,
       A.favourite_section as author_favourite_section,
       P.id as post_id,
       P.blog_id as post_blog_id,
       P.author_id as post_author_id,
       P.created_on as post_created_on,
       P.section as post_section,
       P.subject as post_subject,
       P.draft as draft,
       P.body as post_body,
       C.id as comment_id,
       C.post_id as comment_post_id,
       C.name as comment_name,
       C.comment as comment_text,
       T.id as tag_id,
       T.name as tag_name
  from Blog B
       left outer join Author A on B.author_id = A.id
       left outer join Post P on B.id = P.blog_id
       left outer join Comment C on P.id = C.post_id
       left outer join Post_Tag PT on PT.post_id = P.id
       left outer join Tag T on PT.tag_id = T.id
  where B.id = #{id}
</select>
```xml
<!-- 非常复杂的结果映射 -->
<resultMap id="detailedBlogResultMap" type="Blog">
  <constructor>
    <idArg column="blog_id" javaType="int"/>
  </constructor>
  <result property="title" column="blog_title"/>
  <association property="author" javaType="Author">
    <id property="id" column="author_id"/>
    <result property="username" column="author_username"/>
    <result property="password" column="author_password"/>
    <result property="email" column="author_email"/>
    <result property="bio" column="author_bio"/>
    <result property="favouriteSection" column="author_favourite_section"/>
  </association>
  <collection property="posts" ofType="Post">
    <id property="id" column="post_id"/>
    <result property="subject" column="post_subject"/>
    <association property="author" javaType="Author"/>
    <collection property="comments" ofType="Comment">
      <id property="id" column="comment_id"/>
    </collection>
    <collection property="tags" ofType="Tag" >
      <id property="id" column="tag_id"/>
    </collection>
    <discriminator javaType="int" column="draft">
      <case value="1" resultType="DraftPost"/>
    </discriminator>
  </collection>
</resultMap>
```
* * *
