#Trigger
[toc]
##Trigger公共属性
+ jobKey属性：当trigger触发时被执行的job的身份；
+ startTime属性：设置trigger第一次触发的时间；该属性的值是java.util.Date类型，表示某个指定的时间点；有些类型的trigger，会在设置的startTime时立即触发，有些类型的trigger，表示其触发是在startTime之后开始生效。比如，现在是1月份，你设置了一个trigger–“在每个月的第5天执行”，然后你将startTime属性设置为4月1号，则该trigger第一次触发会是在几个月以后了(即4月5号)。
+ endTime属性：表示trigger失效的时间点。比如，”每月第5天执行”的trigger，如果其endTime是7月1号，则其最后一次执行时间是6月5号。

##Trigger其他属性

* priority属性
>如果trigger很多(或者Quartz线程池的工作线程太少) , Quartz可能没有足够的资源同时触发所有的trigger;
>这种情况下，你可能希望控制哪些trigger优先使用Quartz的工作线程，要达到该目的，可以在trigger上设置priority属性。
>比如，你有N个trigger需要同时触发，但只有Z个工作线程，优先级最高的Z个trigger会被首先触发。如果没有为trigger设置优先级，trigger使用默认优先级，值为5；priority属性的值可以是任意整数，正数、负数都可以。
>>注意：只有同时触发的trigger之间才会比较优先级。10:59触发的trigger总是在11:00触发的trigger之前执行。
>>注意：如果trigger是可恢复的，在恢复后再调度时，优先级与原trigger是一样的。

* misfire属性
>如果scheduler关闭了，或者Quartz线程池中没有可用的线程来执行job，此时持久性的trigger就会错过(miss)其触发时间，即错过触发(misfire)。
>不同类型的trigger，有不同的misfire机制。它们默认都使用“智能机制(smart policy)”，即根据trigger的类型和配置动态调整行为。
>当scheduler启动的时候，查询所有错过触发(misfire)的持久性trigger。然后根据它们各自的misfire机制更新trigger的信息。


##Simple Trigger
SimpleTrigger可以满足的调度需求是：在具体的时间点执行一次，或者在具体的时间点执行，并且以指定的间隔重复执行若干次。

SimpleTrigger的属性包括：开始时间、结束时间、重复次数以及重复的间隔。

重复次数，可以是0、正整数，以及常量SimpleTrigger.REPEAT_INDEFINITELY。重复的间隔，必须是0，或者long型的正数，表示毫秒。注意，如果重复间隔为0，trigger将会以重复次数并发执行(或者以scheduler可以处理的近似并发数)。

##CronTrigger
Cron-Expressions用于配置CronTrigger的实例。Cron Expressions是由七个子表达式组成的字符串，用于描述日程表的各个细节。这些子表达式用空格分隔，并表示：

1 Seconds
2 Minutes
3 Hours
4 Day-of-Month
5 Month
6 Day-of-Week
7 Year (optional field)

+ 单个子表达式可以包含范围和/或列表。例如，可以用“MON-FRI”
+ 通配符（\*字符）可用于说明该字段的“每个”可能的值。
+ '/'字符可用于指定值的增量。例如，如果在“分钟”字段中输入“0/15”，则表示“每隔15分钟，从零开始”。
+ '？' 字符是允许的日期和星期几字段。用于指定“无特定值”。
+ L”字符允许用于月日和星期几字段。例如，“月”字段中的“L”表示“月的最后一天”
+ “W”用于指定最近给定日期的工作日（星期一至星期五）。
+ '＃'用于指定本月的“第n个”XXX工作日。例如，“星期几”字段中的“6＃3”或“FRI＃3”的值表示“本月的第三个星期五”。

>构建CronTriggers
>CronTrigger实例使用TriggerBuilder（用于触发器的主要属性）和CronScheduleBuilder（对于CronTrigger特定的属性）构建。

```java
//建立一个触发器，每隔一分钟，每天上午8点至下午5点之间：
trigger = newTrigger()
    .withIdentity("trigger3", "group1")
    .withSchedule(cronSchedule("0 0/2 8-17 * * ?"))
    .forJob("myJob", "group1")
    .build();
```

##TriggerListeners和JobListeners
用于根据调度程序中发生的事件执行操作。您可能猜到，TriggerListeners接收到与触发器（trigger）相关的事件，JobListeners 接收与jobs相关的事件。
与触发相关的事件包括：触发器触发，触发失灵,触发完成

##SchedulerListeners
与计划程序相关的事件包括：添加job/触发器，删除job/触发器，调度程序中的严重错误，关闭调度程序的通知等。

