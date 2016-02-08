BINARY=db-migration
#=======================================================

build: vendor
	@php build-par.php --bin="$(BINARY)"

vendor:
	composer install --no-dev

clean:
	-rm $(BINARY)

clean-vendor: clean
	-rm -rf vendor

install: $(BINARY)
	cp $(BINARY) /usr/local/bin/
