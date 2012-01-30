## Introduction

This is a project trying to build an auto scale architecture of PHP-Resque.

## Design

Expected behavior:

* Trigger ```AFTERENQUEUE``` to check the total job number of this queue.

* If the number larger than ```15``` than check the total number of workers involved in this queue.

* If the worker number is not enough, create one or more workers.

  * If there are more than one server, divided the number equally to each server.
  
  * In the mean time, try to create workers that deal the same queues on each server.

* Trigger ```AFTERPERFORM``` to check the total job number and worker number, close the useless ones.

## Disclaimer

For now it's all experimental design.
