# 延时队列说明 #

## 什么是延时队列？ ##
与日常中我们所用的消息队列不同，延时队列是可以规定一段时间后进行消息的消费。

## 什么时候用延时队列？ ##
比如订单通知tp支付成功，如果tp在一个时间段内没有回调支付成功订单确认的消息，那么需要我方在一段时间后主动的进行订单状态的拉取。
那么有些不需要立刻执行的任务，则可以用延时队列解决。

## 相关名词 ##
- 1.环形队列：延时队列为一个环形的队列，并均等（时间）分n个槽（Slot）
- 2.槽数（SlotNumber）:一圈队列槽的个数
- 3.圈数（CircleNumber）：用来记录延时队列执行了多少圈
- 4.指针（Probe）：用来记录当前圈中执行到了那个槽
- 5.Timer：时间维度的控制者，每执行一次扫描一个槽

## 实现流程 ##
- 1.首先把一个环分成若干个槽，得到SlotNumber，初始化圈数（CircleNumber）和指针（Probe）为0（重启可以略过）。
- 2.每圈的每个槽上都是一个队列（防止冲突）。
- 3.启动一个定时Timer（linux定时任务或者相关语言的定时器实现）。
- 4.由于Timer对时间间隔的控制，每当一个间隔执行时，通过圈数（CircleNumber）和指针（Probe）计算当前要读哪个槽（其实就是确定队列的名称）。
- 5.被读到的槽，消费这个槽队列中的所有消息（异步，如把队列中的内容全部放置到可执行队列中）。
- 6.计算新的圈数（CircleNumber）和指针（Probe），并记录。
- 7.重复4-6的动作

## Probe,SlotNumber计算： ##
```php
if(Probe > SlotNumber){
    CircleNumber++;
    Probe = Probe % SlotNumber;
}
return Probe , SlotNumber;
```
