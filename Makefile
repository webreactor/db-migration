BINARY=db-migration
#=======================================================

build: vendor
	@php build-par.php --bin="$(BINARY)"

vendor:
	composer install

clean:
	-rm $(BINARY)

clean-vendor: clean
	-rm -rf vendor
