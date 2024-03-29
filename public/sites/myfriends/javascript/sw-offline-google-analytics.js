/*
 Copyright 2016 Google Inc. All Rights Reserved.
 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
*/

this.goog = this.goog || {}
this.goog.offlineGoogleAnalytics = (function () {
  'use strict'

  var constants = {
    CACHE_NAME: 'offline-google-analytics',
    IDB: { NAME: 'offline-google-analytics', STORE: 'urls', VERSION: 1 },
    MAX_ANALYTICS_BATCH_SIZE: 20,
    STOP_RETRYING_AFTER: 172800000,
    URL: {
      ANALYTICS_JS_PATH: '/analytics.js',
      COLLECT_PATH: '/collect',
      HOST: 'www.google-analytics.com',
    },
  }

  function createCommonjsModule(fn, module) {
    return (
      (module = { exports: {} }), fn(module, module.exports), module.exports
    )
  }

  var idb = createCommonjsModule(function (a) {
    'use strict'
    ;(function () {
      function b(r) {
        return Array.prototype.slice.call(r)
      }
      function c(r) {
        return new Promise(function (s, t) {
          ;(r.onsuccess = function () {
            s(r.result)
          }),
            (r.onerror = function () {
              t(r.error)
            })
        })
      }
      function d(r, s, t) {
        var u,
          v = new Promise(function (w, x) {
            ;(u = r[s].apply(r, t)), c(u).then(w, x)
          })
        return (v.request = u), v
      }
      function e(r, s, t) {
        var u = d(r, s, t)
        return u.then(function (v) {
          return v ? new k(v, u.request) : void 0
        })
      }
      function f(r, s, t) {
        t.forEach(function (u) {
          Object.defineProperty(r.prototype, u, {
            get: function () {
              return this[s][u]
            },
            set: function (v) {
              this[s][u] = v
            },
          })
        })
      }
      function g(r, s, t, u) {
        u.forEach(function (v) {
          v in t.prototype &&
            (r.prototype[v] = function () {
              return d(this[s], v, arguments)
            })
        })
      }
      function h(r, s, t, u) {
        u.forEach(function (v) {
          v in t.prototype &&
            (r.prototype[v] = function () {
              return this[s][v].apply(this[s], arguments)
            })
        })
      }
      function i(r, s, t, u) {
        u.forEach(function (v) {
          v in t.prototype &&
            (r.prototype[v] = function () {
              return e(this[s], v, arguments)
            })
        })
      }
      function j(r) {
        this._index = r
      }
      function k(r, s) {
        ;(this._cursor = r), (this._request = s)
      }
      function l(r) {
        this._store = r
      }
      function m(r) {
        ;(this._tx = r),
          (this.complete = new Promise(function (s, t) {
            ;(r.oncomplete = function () {
              s()
            }),
              (r.onerror = function () {
                t(r.error)
              }),
              (r.onabort = function () {
                t(r.error)
              })
          }))
      }
      function n(r, s, t) {
        ;(this._db = r), (this.oldVersion = s), (this.transaction = new m(t))
      }
      function o(r) {
        this._db = r
      }
      f(j, '_index', ['name', 'keyPath', 'multiEntry', 'unique']),
        g(j, '_index', IDBIndex, [
          'get',
          'getKey',
          'getAll',
          'getAllKeys',
          'count',
        ]),
        i(j, '_index', IDBIndex, ['openCursor', 'openKeyCursor']),
        f(k, '_cursor', ['direction', 'key', 'primaryKey', 'value']),
        g(k, '_cursor', IDBCursor, ['update', 'delete']),
        ['advance', 'continue', 'continuePrimaryKey'].forEach(function (r) {
          r in IDBCursor.prototype &&
            (k.prototype[r] = function () {
              var s = this,
                t = arguments
              return Promise.resolve().then(function () {
                return (
                  s._cursor[r].apply(s._cursor, t),
                  c(s._request).then(function (u) {
                    return u ? new k(u, s._request) : void 0
                  })
                )
              })
            })
        }),
        (l.prototype.createIndex = function () {
          return new j(this._store.createIndex.apply(this._store, arguments))
        }),
        (l.prototype.index = function () {
          return new j(this._store.index.apply(this._store, arguments))
        }),
        f(l, '_store', ['name', 'keyPath', 'indexNames', 'autoIncrement']),
        g(l, '_store', IDBObjectStore, [
          'put',
          'add',
          'delete',
          'clear',
          'get',
          'getAll',
          'getKey',
          'getAllKeys',
          'count',
        ]),
        i(l, '_store', IDBObjectStore, ['openCursor', 'openKeyCursor']),
        h(l, '_store', IDBObjectStore, ['deleteIndex']),
        (m.prototype.objectStore = function () {
          return new l(this._tx.objectStore.apply(this._tx, arguments))
        }),
        f(m, '_tx', ['objectStoreNames', 'mode']),
        h(m, '_tx', IDBTransaction, ['abort']),
        (n.prototype.createObjectStore = function () {
          return new l(this._db.createObjectStore.apply(this._db, arguments))
        }),
        f(n, '_db', ['name', 'version', 'objectStoreNames']),
        h(n, '_db', IDBDatabase, ['deleteObjectStore', 'close']),
        (o.prototype.transaction = function () {
          return new m(this._db.transaction.apply(this._db, arguments))
        }),
        f(o, '_db', ['name', 'version', 'objectStoreNames']),
        h(o, '_db', IDBDatabase, ['close']),
        ['openCursor', 'openKeyCursor'].forEach(function (r) {
          ;[l, j].forEach(function (s) {
            s.prototype[r.replace('open', 'iterate')] = function () {
              var t = b(arguments),
                u = t[t.length - 1],
                v = this._store || this._index,
                w = v[r].apply(v, t.slice(0, -1))
              w.onsuccess = function () {
                u(w.result)
              }
            }
          })
        }),
        [j, l].forEach(function (r) {
          r.prototype.getAll ||
            (r.prototype.getAll = function (s, t) {
              var u = this,
                v = []
              return new Promise(function (w) {
                u.iterateCursor(s, function (x) {
                  return x
                    ? (v.push(x.value),
                      void 0 !== t && v.length == t
                        ? void w(v)
                        : void x.continue())
                    : void w(v)
                })
              })
            })
        })
      var q = {
        open: function (r, s, t) {
          var u = d(indexedDB, 'open', [r, s]),
            v = u.request
          return (
            (v.onupgradeneeded = function (w) {
              t && t(new n(v.result, w.oldVersion, v.transaction))
            }),
            u.then(function (w) {
              return new o(w)
            })
          )
        },
        delete: function (r) {
          return d(indexedDB, 'deleteDatabase', [r])
        },
      }
      ;(a.exports = q), (a.exports.default = a.exports)
    })()
  })

  class IDBHelper {
    constructor(a, b, c) {
      if (a == void 0 || b == void 0 || c == void 0)
        throw Error(
          'name, version, storeName must be passed to the constructor.'
        )
      ;(this._name = a), (this._version = b), (this._storeName = c)
    }
    _getDb() {
      return this._dbPromise
        ? this._dbPromise
        : ((this._dbPromise = idb
            .open(this._name, this._version, (a) => {
              a.createObjectStore(this._storeName)
            })
            .then((a) => {
              return a
            })),
          this._dbPromise)
    }
    close() {
      return this._dbPromise
        ? this._dbPromise.then((a) => {
            a.close(), (this._dbPromise = null)
          })
        : void 0
    }
    put(a, b) {
      return this._getDb().then((c) => {
        const d = c.transaction(this._storeName, 'readwrite'),
          e = d.objectStore(this._storeName)
        return e.put(b, a), d.complete
      })
    }
    delete(a) {
      return this._getDb().then((b) => {
        const c = b.transaction(this._storeName, 'readwrite'),
          d = c.objectStore(this._storeName)
        return d.delete(a), c.complete
      })
    }
    get(a) {
      return this._getDb().then((b) => {
        return b
          .transaction(this._storeName)
          .objectStore(this._storeName)
          .get(a)
      })
    }
    getAllValues() {
      return this._getDb().then((a) => {
        return a
          .transaction(this._storeName)
          .objectStore(this._storeName)
          .getAll()
      })
    }
    getAllKeys() {
      return this._getDb().then((a) => {
        return a
          .transaction(this._storeName)
          .objectStore(this._storeName)
          .getAllKeys()
      })
    }
  }

  const idbHelper = new IDBHelper(
    constants.IDB.NAME,
    constants.IDB.VERSION,
    constants.IDB.STORE
  )
  var enqueueRequest = (a, b) => {
    const c = new URL(a.url)
    return a.text().then((d) => {
      return d && (c.search = d), idbHelper.put(c.toString(), b || Date.now())
    })
  }

  class LogGroup {
    constructor({ title: a, isPrimary: b } = {}) {
      ;(this._isPrimary = b || !1),
        (this._groupTitle = a || ''),
        (this._logs = []),
        (this._childGroups = []),
        (this._isFirefox = !1),
        /Firefox\/\d*\.\d*/.exec(navigator.userAgent) && (this._isFirefox = !0),
        (this._isEdge = !1),
        /Edge\/\d*\.\d*/.exec(navigator.userAgent) && (this._isEdge = !0)
    }
    addLog(a) {
      this._logs.push(a)
    }
    addChildGroup(a) {
      0 === a._logs.length || this._childGroups.push(a)
    }
    print() {
      return this._isEdge
        ? void this._printEdgeFriendly()
        : void (this._openGroup(),
          this._logs.forEach((a) => {
            this._printLogDetails(a)
          }),
          this._childGroups.forEach((a) => {
            a.print()
          }),
          this._closeGroup())
    }
    _printEdgeFriendly() {
      this._logs.forEach((a) => {
        let c = a.message
        'string' == typeof c && (c = c.replace(/%c/g, ''))
        const d = [c]
        a.error && d.push(a.error), a.args && d.push(a.args)
        const e = a.logFunc || console.log
        e(...d)
      }),
        this._childGroups.forEach((a) => {
          a.print()
        })
    }
    _printLogDetails(a) {
      const b = a.logFunc ? a.logFunc : console.log
      let c = a.message,
        d = [c]
      a.colors && !this._isEdge && (d = d.concat(a.colors)),
        a.args && (d = d.concat(a.args)),
        b(...d)
    }
    _openGroup() {
      if (this._isPrimary) {
        if (0 === this._childGroups.length) return
        const a = this._logs.shift()
        if (this._isFirefox) return void this._printLogDetails(a)
        ;(a.logFunc = console.group), this._printLogDetails(a)
      } else console.groupCollapsed(this._groupTitle)
    }
    _closeGroup() {
      ;(this._isPrimary && 0 === this._childGroups.length) || console.groupEnd()
    }
  }

  function isServiceWorkerGlobalScope() {
    return (
      'ServiceWorkerGlobalScope' in self &&
      self instanceof ServiceWorkerGlobalScope
    )
  }
  function isDevBuild() {
    return `dev` == `prod`
  }
  function isLocalhost() {
    return !!(
      'localhost' === location.hostname ||
      '[::1]' === location.hostname ||
      location.hostname.match(
        /^127(?:\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}$/
      )
    )
  }
  var environment = { isDevBuild, isLocalhost, isServiceWorkerGlobalScope }

  ;(self.goog = self.goog || {}),
    (self.goog.LOG_LEVEL = self.goog.LOG_LEVEL || {
      none: -1,
      verbose: 0,
      debug: 1,
      warn: 2,
      error: 3,
    })
  const LIGHT_GREY = `#bdc3c7`
  const DARK_GREY = `#7f8c8d`
  const LIGHT_GREEN = `#2ecc71`
  const LIGHT_YELLOW = `#f1c40f`
  const LIGHT_RED = `#e74c3c`
  const LIGHT_BLUE = `#3498db`
  class LogHelper {
    constructor() {
      this._defaultLogLevel = environment.isDevBuild()
        ? self.goog.LOG_LEVEL.debug
        : self.goog.LOG_LEVEL.warn
    }
    log(a) {
      this._printMessage(self.goog.LOG_LEVEL.verbose, a)
    }
    debug(a) {
      this._printMessage(self.goog.LOG_LEVEL.debug, a)
    }
    warn(a) {
      this._printMessage(self.goog.LOG_LEVEL.warn, a)
    }
    error(a) {
      this._printMessage(self.goog.LOG_LEVEL.error, a)
    }
    _printMessage(a, b) {
      if (this._shouldLogMessage(a, b)) {
        const c = this._getAllLogGroups(a, b)
        c.print()
      }
    }
    _getAllLogGroups(a, b) {
      const c = new LogGroup({ isPrimary: !0, title: 'sw-helpers log.' }),
        d = this._getPrimaryMessageDetails(a, b)
      if ((c.addLog(d), b.error)) {
        const f = { message: b.error, logFunc: console.error }
        c.addLog(f)
      }
      const e = new LogGroup({ title: 'Extra Information.' })
      if (b.that && b.that.constructor && b.that.constructor.name) {
        const f = b.that.constructor.name
        e.addLog(this._getKeyValueDetails('class', f))
      }
      return (
        b.data &&
          ('object' != typeof b.data || b.data instanceof Array
            ? e.addLog(this._getKeyValueDetails('additionalData', b.data))
            : Object.keys(b.data).forEach((f) => {
                e.addLog(this._getKeyValueDetails(f, b.data[f]))
              })),
        c.addChildGroup(e),
        c
      )
    }
    _getKeyValueDetails(a, b) {
      return { message: `%c${a}: `, colors: [`color: ${LIGHT_BLUE}`], args: b }
    }
    _getPrimaryMessageDetails(a, b) {
      let c, d
      a === self.goog.LOG_LEVEL.verbose
        ? ((c = 'Info'), (d = LIGHT_GREY))
        : a === self.goog.LOG_LEVEL.debug
        ? ((c = 'Debug'), (d = LIGHT_GREEN))
        : a === self.goog.LOG_LEVEL.warn
        ? ((c = 'Warn'), (d = LIGHT_YELLOW))
        : a === self.goog.LOG_LEVEL.error
        ? ((c = 'Error'), (d = LIGHT_RED))
        : void 0
      let e = `%c🔧 %c[${c}]`
      const f = [`color: ${LIGHT_GREY}`, `color: ${d}`]
      let g
      return (
        'string' == typeof b ? (g = b) : b.message && (g = b.message),
        g &&
          ((g = g.replace(/\s+/g, ' ')),
          (e += `%c ${g}`),
          f.push(`color: ${DARK_GREY}; font-weight: normal`)),
        { message: e, colors: f }
      )
    }
    _shouldLogMessage(a, b) {
      if (!b) return !1
      let c = this._defaultLogLevel
      return (
        self &&
          self.goog &&
          'number' == typeof self.goog.logLevel &&
          (c = self.goog.logLevel),
        c === self.goog.LOG_LEVEL.none || a < c ? !1 : !0
      )
    }
  }
  var logHelper = new LogHelper()

  const idbHelper$1 = new IDBHelper(
    constants.IDB.NAME,
    constants.IDB.VERSION,
    constants.IDB.STORE
  )
  var replayQueuedRequests = (a) => {
    return (
      (a = a || {}),
      idbHelper$1.getAllKeys().then((b) => {
        return Promise.all(
          b.map((c) => {
            return idbHelper$1
              .get(c)
              .then((d) => {
                const e = Date.now() - d,
                  f = new URL(c)
                if (e > constants.STOP_RETRYING_AFTER) return
                if (!('searchParams' in f)) return
                let g = a.parameterOverrides || {}
                ;(g.qt = e),
                  Object.keys(g)
                    .sort()
                    .forEach((i) => {
                      f.searchParams.set(i, g[i])
                    })
                let h = a.hitFilter
                if ('function' == typeof h)
                  try {
                    h(f.searchParams)
                  } catch (i) {
                    return
                  }
                return fetch(f.toString())
              })
              .then(() => idbHelper$1.delete(c))
          })
        )
      })
    )
  }

  const initialize = (a) => {
    a = a || {}
    let b = !1
    self.addEventListener('fetch', (c) => {
      const d = new URL(c.request.url),
        e = c.request
      if (d.hostname === constants.URL.HOST)
        if (d.pathname === constants.URL.COLLECT_PATH) {
          const f = e.clone()
          c.respondWith(
            fetch(e).then(
              (g) => {
                return b && replayQueuedRequests(a), (b = !1), g
              },
              () => {
                return (
                  logHelper.log('Enqueuing failed request...'),
                  (b = !0),
                  enqueueRequest(f).then(() => Response.error())
                )
              }
            )
          )
        } else
          d.pathname === constants.URL.ANALYTICS_JS_PATH &&
            c.respondWith(
              caches.open(constants.CACHE_NAME).then((f) => {
                return fetch(e)
                  .then((g) => {
                    return f.put(e, g.clone()).then(() => g)
                  })
                  .catch((g) => {
                    return logHelper.error(g), f.match(e)
                  })
              })
            )
    }),
      replayQueuedRequests(a)
  }
  var index = { initialize }

  return index
})()
//# sourceMappingURL=sw-offline-google-analytics.prod.v0.0.25.js.map
