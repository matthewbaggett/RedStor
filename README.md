# RedStor

[![Build Status](https://travis-ci.org/matthewbaggett/RedStor.svg?branch=master)](https://travis-ci.org/matthewbaggett/RedStor)

## Todo list:
 * App/User/Password authentication across the redis interface. 
   * In RedStor redis socket worker
   * In Gateway http requests
 * Rate limit usage counting.
 * Rate limit "grow back" worker.
 * Rate limit enforcement.
 * Redis2SQL flusher worker.
 * SQL2Redis lookup on fetch miss.
 * Redis2Trash entity expirer worker.
 * Redis2Solr flusher worker.
 * Solr2Redis Search functionality that supports more than just returning everything.