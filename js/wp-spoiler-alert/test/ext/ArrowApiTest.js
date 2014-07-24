var shim           = require('es5-shim');
var ArrowApi       = require('../../app/ext/ArrowApi').ArrowApi;
var PromiseHandler = require('../../app/ext/ArrowApi').PromiseHandler;
var expect         = require('chai').expect;
var querystring    = require('querystring');
var urlstring      = require('url');
var Promise        = require('es6-promise').Promise;
var $              = require('jquery');
require('script!../sinon');

describe('PromiseHandler', function() {

  var handler;
  var promise = {
    then: function(callback) {
      this.thenCallback = callback;
      return this;
    },
    fail: function(callback) {
      this.failCallback = callback;
      return this;
    }
  };

  it('chains callbacks for promise', function() {
    handler = new PromiseHandler(promise);
    expect(promise.thenCallback).to.be.a('function');
    expect(promise.failCallback).to.be.a('function');
  });

  it('stores resolve and reject callbacks', function() {
    handler = new PromiseHandler(promise);
    handler.handle('resolve-func', 'reject-func');

    expect(handler.resolve).to.equal('resolve-func');
    expect(handler.reject).to.equal('reject-func');
  });

  it('will reject if logged out ajax response', function() {
    var rejectReason;
    handler = new PromiseHandler(promise);
    handler.reject = function(reason) {
      rejectReason = reason;
    };

    handler.onAjaxSuccess('0');
    expect(rejectReason).to.equal('not_logged_in');
  });

  it('will reject if arrow error response', function() {
    var rejectReason;
    handler = new PromiseHandler(promise);
    handler.reject = function(reason) {
      rejectReason = reason;
    };

    var response = {
      success: false,
      data: {
        error: 'foo-error'
      }
    };
    handler.onAjaxSuccess(response);
    expect(rejectReason).to.equal('foo-error');
  });

  it('will resolve if arrow success response', function() {
    var data;
    handler = new PromiseHandler(promise);
    handler.resolve = function(response) {
      data = response;
    };

    var response = {
      success: true,
      data: {
        a: 1,
        b: 2
      }
    };
    handler.onAjaxSuccess(response);
    expect(data).to.eql({a:1, b:2});
  });

  it('will reject if ajax timeout', function() {
    var rejectReason;
    handler = new PromiseHandler(promise);
    handler.reject = function(reason) {
      rejectReason = reason;
    };

    handler.onAjaxFailure({statusText: 'timeout'});
    expect(rejectReason).to.equal('request_timeout');
  });

  it('will reject with returned error on ajax failure', function() {
    var rejectReason;
    handler = new PromiseHandler(promise);
    handler.reject = function(reason) {
      rejectReason = reason;
    };

    var response = {
      responseJSON: {
        data: {
          error: 'foo-error'
        }
      }
    };
    handler.onAjaxFailure(response);
    expect(rejectReason).to.equal('foo-error');
  });

  it('will reject with unknown response on invalid server response', function() {
    var rejectReason;
    handler = new PromiseHandler(promise);
    handler.reject = function(reason) {
      rejectReason = reason;
    };

    var response = {
    };
    handler.onAjaxFailure(response);
    expect(rejectReason).to.equal('unknown_response');
  });

});

describe('ArrowApi', function() {

  var api;
  var config = {
    apiEndpoint: 'http://localhost/api',
    nonce: 'foo'
  };

  beforeEach(function() {
    api = new ArrowApi(config);
  });

  it('stores config object', function() {
    api = new ArrowApi('config');
    expect(api.config).to.equal('config');
  });

  describe('#urlFor', function() {

    it('builds url to apiEndpoint', function() {
      config.apiEndpoint = 'api';
      var url = api.urlFor('things', 'all');
      expect(url).to.match(/^api/);
    });

    context('with api endpoint without args', function() {

      var url;
      var params;

      before(function() {
        config.apiEndpoint = 'http://localhost/api';
        config.nonce       = '123456';

        url    = api.urlFor('things', 'all');
        params = urlstring.parse(url, true).query;
      });

      it('has a nonce param', function() {
        expect(params.nonce).to.equal('123456');
      });

      it('has a controller param', function() {
        expect(params.controller).to.equal('things');
      });

      it('has an operation param', function() {
        expect(params.operation).to.equal('all');
      });

    });

    context('with api endpoint with args', function() {

      var url;
      var params;

      before(function() {
        config.apiEndpoint = 'http://localhost/api?admin=1&action=foo';
        config.nonce       = '123456';

        url    = api.urlFor('things', 'all');
        params = urlstring.parse(url, true).query;
      });

      it('has an admin param', function() {
        expect(params.admin).to.equal('1');
      });

      it('has an action param', function() {
        expect(params.action).to.equal('foo');
      });

      it('has a nonce param', function() {
        expect(params.nonce).to.equal('123456');
      });

      it('has a controller param', function() {
        expect(params.controller).to.equal('things');
      });

      it('has an operation param', function() {
        expect(params.operation).to.equal('all');
      });
    });

  });

  describe('#paramsFor', function() {

    var params;

    context('without params argument', function() {
      before(function() {
        config.apiEndpoint = 'api';
        params = api.paramsFor('things', 'all');
      });

      it('has a url', function() {
        expect(params.url).to.match(/^api/);
      });
    });

    context('with POST params', function() {

      before(function() {
        config.apiEndpoint = 'api';
        params = api.paramsFor('things', 'all', {type: 'POST', 'data': {a:1,b:2}});
      });

      it('has a url', function() {
        expect(params.url).to.match(/^api/);
      });

      it('has stringified data', function() {
        expect(params.data).to.equal('{"a":1,"b":2}');
      });

    });

  });

  describe('#request', function() {

    var sandbox;

    beforeEach(function() {
      sandbox = sinon.sandbox.create();
    });

    afterEach(function() {
      sandbox.restore();
    });

    it('returns a promise', function() {
      var stub = sandbox.stub($, 'ajax');
      var deferred = $.Deferred();

      stub.returns(deferred);

      var promise = api.request('things', 'all');
      expect(promise).to.be.instanceof(Promise);
    });

  });

  describe('#resource', function() {

    var sandbox;
    var deferred;

    beforeEach(function() {
      sandbox = sinon.sandbox.create();
      var stub = sandbox.stub($, 'ajax');
      deferred = $.Deferred();

      stub.returns(deferred);
    });

    afterEach(function() {
      sandbox.restore();
    });

    it('can get all items of resources', function() {
      var promise = api.all('things');
      var result;

      promise = promise.then(function(data) {
        result = data;
        expect(result).to.equal('foo');
      });

      var response = {
        success: true,
        data: 'foo'
      };

      deferred.resolve(response);
      return promise;
    });

    it('can get one item of resource', function() {
      var promise = api.get('things', { id: 1 });
      var result;

      promise = promise.then(function(data) {
        result = data;
        expect(result).to.equal('foo-1');
      });

      var response = {
        success: true,
        data: 'foo-1'
      };

      deferred.resolve(response);
      return promise;
    });

    it('can create new item to resource',  function() {
      var promise = api.post('things', { foo: 'bar' });
      var result;

      promise = promise.then(function(data) {
        result = data;
        expect(result).to.equal('foo-bar');
      });

      var response = {
        success: true,
        data: 'foo-bar'
      };

      deferred.resolve(response);
      return promise;
    });

    it('can update item of resource',  function() {
      var promise = api.put('things', { id: 1, foo: 'bar', bar: 'foo' });
      var result;

      promise = promise.then(function(data) {
        result = data;
        expect(result).to.equal('foo-bar');
      });

      var response = {
        success: true,
        data: 'foo-bar'
      };

      deferred.resolve(response);
      return promise;
    });

    it('can patch key of item of resource',  function() {
      var promise = api.patch('things', { id: 1, foo: 'bar' });
      var result;

      promise = promise.then(function(data) {
        result = data;
        expect(result).to.equal('foo-bar');
      });

      var response = {
        success: true,
        data: 'foo-bar'
      };

      deferred.resolve(response);
      return promise;
    });

    it('can delete item of resource',  function() {
      var promise = api.delete('things', { id: 1 });
      var result;

      promise = promise.then(function(data) {
        result = data;
        expect(result).to.equal('foo-1');
      });

      var response = {
        success: true,
        data: 'foo-1'
      };

      deferred.resolve(response);
      return promise;
    });
  });

});
