<?php

/**
 * Simple Object Oriented Interface for traversing a CSV file.
 *
 * @author William Wheatley <will@iwill.id.au>
 */
class EasyCSV implements Iterator {

	private $file_handle;

	private $filename;

	private $columns;
	
	private $column_count;

	private $row;

	private $row_number;

	private $access_method;

	private $row_buffer;

	private $header;

    private $delimiter;

	public function __construct($filename, $process_header = true, $method = 'class') {
		$this->filename = $filename;
		$this->file_handle = false;
		$this->columns = array();
		$this->row = false;
		$this->row_number = 0;
		$this->header = true;
        $this->delimiter = ',';
		$this->set_access_method($method);
		$this->row_buffer = 5000;
		$this->process_header($process_header);
	}

	public function process_header($header) {
		if ($header) {
			$this->header = true;
		}
		else {
			$this->access_method = 'array';
			$this->header = false;
		}
	}
	public function set_row_buffer($buffer) {
		$this->row_buffer = intval($buffer);
	}

    public function set_delimiter($delimiter) {
        $this->delimiter = $delimiter;
    }

	public function set_access_method($method) {
		switch ($method) {
			case 'array':
				$this->access_method = 'array';
				break;
			case 'class':
			default:
				if ($this->header) {
					$this->access_method = 'class';
				}
				else {
					trigger_error('Cannot set CSV Access Method to "class" when not reading the header', E_USER_WARNING);
					$this->access_method = 'array';
				}

		}
	}

	static public function open($filename) {
		return new EasyCSV($filename);
	}

	public function __get($name) {
		if (array_key_exists($name, $this->columns)) {
			if ($this->row) {
				if (array_key_exists($this->columns[$name], $this->row)) {
					if (strlen($this->row[$this->columns[$name]])) {
						return $this->row[$this->columns[$name]];
					}
					else {
						return false;
					}
				}
			}
			else {
				trigger_error('Cannot Access a Row without loading the file', E_USER_WARNING);
				return false;
			}
		}
		else {
			trigger_error('Invalid Column Name: '.$name, E_USER_WARNING);
			return false;
		}
	}

	public function __isset($name) {
		if (array_key_exists($name, $this->columns)) {
			if ($this->row) {
				if (strlen($this->row[$this->columns[$name]])) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				trigger_error('Cannot Access a Row without loading the file', E_USER_WARNING);
				return false;
			}
		}
	}

	private function read_column_titles() {
		$row = fgetcsv($this->file_handle, $this->row_buffer, $this->delimiter);
        $blank_count = 0;
		foreach ($row as $column_name) {
            
			$column_name = trim($column_name);
			$column_name = str_replace(' ','_',$column_name);
            if (!strlen($column_name)) {
                $blank_count++;
                $column_name = 'Blank_'.$blank_count;
            }
			if (array_key_exists($column_name, $this->columns)) {
				trigger_error("Duplicate column name '$column_name' in file '$this->filename'", E_USER_ERROR);
			}
			else {
				$this->columns[$column_name] = count($this->columns);
			}
		}
		$this->column_count = count($this->columns);
	}

	public function rewind() {
		if ($this->file_handle) {
			rewind($this->file_handle);
			// Read in the Column headers
			if ($this->header) {
				fgetcsv($this->file_handle, $this->row_buffer, $this->delimiter);
				$this->row_number = 1;
			}
			else {
				$this->row_number = 0;
			}
			$this->next();
		}
		else {
			$this->file_handle = fopen($this->filename, 'r');
			if ($this->file_handle) {
				if ($this->header) {
					$this->read_column_titles();
					$this->row_number = 1;
				}
				else {
					$this->row_number = 0;
				}
				$this->next();
			}
			else {
				trigger_error('Could not open file: '.$this->filename, E_USER_ERROR);
			}
		}
	}

	public function valid() {
		if ($this->row) {
			return true;
		}
		else {
			return false;
		}
	}

	public function current() {
		if ($this->row) {
			if ($this->access_method == 'array') {
				if ($this->header) {
					$row_count = count($this->row);
					if ($this->column_count == $row_count) {
						return array_combine(array_keys($this->columns), $this->row);
					}
					else if ($this->column_count > $row_count) {
						// Pad out the Row to equal the number of columns
						return array_combine(
									array_keys($this->columns), 
									array_merge($this->row, array_fill($row_count, $this->column_count - $row_count, false)));
					}
					else if ($this->column_count < $row_count) {
						return array_combine(
									array_keys($this->columns), 
									array_slice($this->row, 0, $this->column_count, true));
					}
					return array();
				}
				else {
					return $this->row;
				}
			}
			else {
				return $this;
			}
		}
		else {
			return false;
		}
	}

	public function key() {
		return $this->row_number;
	}

	public function next() {
		$this->row = fgetcsv($this->file_handle, $this->row_buffer, $this->delimiter);
		$this->row_number++;
	}

	public function get_columns() {
		return $this->columns;
	}

}
