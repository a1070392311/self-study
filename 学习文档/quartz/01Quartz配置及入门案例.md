#Quartz配置及入门案例
[toc]
##安装
Maven导入
```xml
<!-- https://mvnrepository.com/artifact/org.quartz-scheduler/quartz -->
<dependency>
    <groupId>org.quartz-scheduler</groupId>
    <artifactId>quartz</artifactId>
    <version>2.3.2</version>
</dependency>

```
或者下载jar包
+ c3p0-0.9.5.3.jar
+ log4j-1.2.16.jar
+ quartz-2.3.1-SNAPSHOT.jar
+ quartz-jobs-2.3.1-SNAPSHOT.jar
+ slf4j-api-1.7.7.jar
+ slf4j-log4j12-1.7.7.jar

<font color="red" size=4>在maven项目使用quartz和在原生java项目中使用quartz调度方法有些许不用，在java9项目中，要记得配置module-info.java文件,防止出现模块之间不能相互访问的问题</font>

##基础概念
>Quartz API
>>Job - 你想要调度器执行的任务组件需要实现的接口
>>JobDetail - 用于定义作业的实例。
>>JobBuilder - 用于定义/构建 JobDetail 实例，用于定义作业的实例。
>>Trigger（即触发器） - 定义执行给定作业的计划的组件。
>>TriggerBuilder - 用于定义/构建触发器实例。
>>Scheduler - 与调度程序交互的主要API。
>>Scheduler - 的生命期，从 SchedulerFactory 创建它时开始，到 Scheduler 调用shutdown() 方法时结束；Scheduler 被创建后，可以增加、删除和列举 Job 和 Trigger，以及执行其它与调度相关的操作（如暂停 Trigger）。但是，Scheduler 只有在调用 start() 方法后，才会真正地触发 trigger（即执行 job）

##简单实例
以下案例基于java9项目，在maven中调用方法有些许不同
+ Job类

```java
package com.my.job;

import java.text.SimpleDateFormat;
import java.util.Date;

import org.quartz.Job;
import org.quartz.JobExecutionContext;
import org.quartz.JobExecutionException;
import org.quartz.JobKey;
import org.quartz.Scheduler;
import org.quartz.SchedulerException;
import org.quartz.SchedulerMetaData;

public class TestJob implements Job {

	@Override
	public void execute(JobExecutionContext context) throws JobExecutionException {
		// TODO Auto-generated method stub
		JobKey key = context.getJobDetail().getKey();
		Scheduler scheduler = context.getScheduler();
		try {
			SchedulerMetaData metaData = scheduler.getMetaData();
			SimpleDateFormat simpleDateFormat = new SimpleDateFormat("yyyy-mm-ss HH:mm:ss");
			Date date = new Date();
			String timeString = simpleDateFormat.format(date);
			System.out.println("This is my job , Time is "+timeString);
		} catch (SchedulerException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

}

```
+ 调用类

```java
package com.my.quartz;

import org.quartz.JobBuilder;
import org.quartz.JobDetail;
import org.quartz.Scheduler;
import org.quartz.SchedulerException;
import org.quartz.SimpleScheduleBuilder;
import org.quartz.SimpleTrigger;
import org.quartz.TriggerBuilder;
import org.quartz.impl.StdSchedulerFactory;

import com.my.job.TestJob;

public class SimpleSchedule {
	public static void main(String[] args) throws SchedulerException, InterruptedException {
		
		Scheduler defaultScheduler = StdSchedulerFactory.getDefaultScheduler();
		JobDetail jobDetail = JobBuilder.newJob(TestJob.class)
										.withIdentity("job1" , "group1")
										.usingJobData("name", "Hello")
										.build();
		SimpleTrigger trigger = TriggerBuilder.newTrigger()
					  .withIdentity("tri1" , "tri-group1")
					  .startNow()
					  .withSchedule(SimpleScheduleBuilder.simpleSchedule().withIntervalInSeconds(2).repeatForever())
					  .build();
		defaultScheduler.scheduleJob(jobDetail, trigger);
		defaultScheduler.start();
		
		Thread.sleep(10000);
		defaultScheduler.shutdown();
	}
}

```

这里可能遇到log4j报错
在src目录下配置log4j.properties文件
```
#这里的stdout配置可以打开控制台输出DEBUG信息
#log4j.rootLogger=debug, R
log4j.rootLogger=debug, stdout , R

log4j.appender.stdout=org.apache.log4j.ConsoleAppender
log4j.appender.stdout.layout=org.apache.log4j.PatternLayout

log4j.appender.stdout.layout.ConversionPattern=%5p - %m%n

log4j.appender.R=org.apache.log4j.RollingFileAppender
log4j.appender.R.File=firestorm.log

log4j.appender.R.MaxFileSize=100KB
log4j.appender.R.MaxBackupIndex=1

log4j.appender.R.layout=org.apache.log4j.PatternLayout
log4j.appender.R.layout.ConversionPattern=%p %t %c - %m%n

log4j.logger.com.codefutures=DEBUG
```