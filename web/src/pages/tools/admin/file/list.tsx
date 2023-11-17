import React, { useRef } from 'react'
import { useTranslate, useDelete } from '@refinedev/core'
import { PrimaryTableCol, Button, Link, Popconfirm, Select, Form } from 'tdesign-react/esm'
import { PageTable, Modal, useSelect, TableRef } from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()
  const { mutate } = useDelete()
  const table = useRef<TableRef>(null)
  const { options, onSearch, queryResult } = useSelect({
    resource: 'tools.fileDir',
    optionLabel: 'name',
    optionValue: 'id',
  })

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      {
        colKey: 'name',
        title: translate('tools.file.fields.name'),
        minWidth: 300,
        cell: ({ row }) => {
          return (
            <div className='flex items-center gap-2'>
              <div>
                {/^image\//.test(row.mime) && (
                  <div className='h-10 w-10 flex items-center justify-center rounded p-2 text-white bg-brand'>
                    <div className='i-tabler:photo h-6 w-6'></div>
                  </div>
                )}
              </div>
              <div className='flex flex-col'>
                <div>{row.name}</div>
                <div className='text-sm text-gray'>{row.mime}</div>
              </div>
            </div>
          )
        },
      },
      {
        colKey: 'size',
        title: translate('tools.file.fields.size'),
        width: 150,
      },
      {
        colKey: 'driver',
        title: translate('tools.file.fields.driver'),
        width: 150,
      },
      {
        colKey: 'time',
        title: translate('tools.file.fields.time'),
        width: 200,
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 160,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <Modal
                title={translate('buttons.edit')}
                trigger={<Link theme='primary'>{translate('buttons.show')}</Link>}
                component={() => import('./save')}
                componentProps={{ id: row.id, dir_id: row.dir_id }}
              />
              <Popconfirm
                content={translate('buttons.confirm')}
                destroyOnClose
                placement='top'
                showArrow
                theme='default'
                onConfirm={() => {
                  mutate({
                    resource: 'tools.fileData',
                    id: row.id,
                  })
                }}
              >
                <Link theme='danger'>{translate('buttons.delete')}</Link>
              </Popconfirm>
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  return (
    <>
      <PageTable
        ref={table}
        columns={columns}
        table={{
          rowKey: 'id',
        }}
        filterRender={() => (
          <>
            <div>
              <Modal
                title={translate('tools.file.fields.addDir')}
                trigger={
                  <Button icon={<div className='i-tabler:plus' />} variant='outline'></Button>
                }
                component={() => import('./group')}
              ></Modal>
            </div>
            <Form.FormItem name='dir_id' initialData={options?.[0]?.value}>
              <Select
                filterable
                clearable
                onSearch={onSearch}
                options={options}
                placeholder={translate('tools.file.placeholder.dir')}
                loading={queryResult.isLoading}
              />
            </Form.FormItem>

            {table.current?.filters?.dir_id && (
              <div className='flex gap-2'>
                <Modal
                  title={translate('tools.file.fields.editGroup')}
                  trigger={
                    <Button icon={<div className='i-tabler:edit' />} variant='outline'></Button>
                  }
                  component={() => import('./group')}
                  componentProps={{
                    id: table.current?.filters.dir_id,
                  }}
                ></Modal>
                <Popconfirm
                  content={translate('buttons.confirm')}
                  destroyOnClose
                  placement='top'
                  showArrow
                  theme='default'
                  onConfirm={() => {
                    mutate({
                      resource: 'tools.file',
                      id: table.current?.filters.dir_id,
                    })
                  }}
                >
                  <Button icon={<div className='i-tabler:trash' />} variant='outline'></Button>
                </Popconfirm>
              </div>
            )}
          </>
        )}
      />
    </>
  )
}

export default List
