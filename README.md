## Introduction

This is a project trying to build an auto scale architecture of PHP-Resque.

## Design

### Expected Behavior

* Trigger ```AFTERENQUEUE``` to check the total job number of this queue.

* If the number larger than ```15``` than check the total number of workers involved in this queue.

* If the worker number is not enough, create one or more workers.

  * If there are more than one server, divided the number equally to each server.
  
  * In the mean time, try to create workers that deal the same queues on each server.

* Trigger ```AFTERPERFORM``` to check the total job number and worker number, close the useless ones.

### Number of Jobs and Workers

* 1~15 jobs => 1 worker

* 16~25 jobs => 2 workers

* 26~40 jobs => 3 workers

* 41~60 jobs => 4 workers

* 60+ jobs => 5 workers

## Disclaimer

For now it's all experimental design.

All numbers and codes are not from production enviroments nor runned benchmarks.
