import React, { useEffect, useRef, useState } from 'react'
import { useTranslate, useDelete, useNavigation, useSelect } from '@refinedev/core'
import { PrimaryTableCol, Button, Link, Popconfirm, Tag, Form, Select } from 'tdesign-react/esm'
import { Modal, PageTable, TableRef } from '@duxweb/dux-refine'
import { Icon } from 'tdesign-icons-react'

const List = () => {
  const translate = useTranslate()
  const { mutate } = useDelete()
  const { create, edit } = useNavigation()
  const table = useRef<TableRef>(null)
  const [init, setInit] = useState(false)
  const { options, onSearch, queryResult } = useSelect({
    resource: 'tools.magicGroup',
    optionLabel: 'label',
    optionValue: 'id',
  })

  useEffect(() => {
    if (!options.length && !init) {
      return
    }
    setInit(() => true)
    table.current?.form?.setFieldsValue({
      menu_id: options[0]?.value,
    })
  }, [init, options])

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      {
        colKey: 'id',
        sorter: true,
        sortType: 'all',
        title: 'ID',
        width: 100,
      },
      {
        colKey: 'group_label',
        title: translate('tools.magic.fields.group'),
        ellipsis: true,
        cell: ({ row }) => {
          return (
            <div className='flex items-center gap-2'>
              <Icon name={row.group_icon} />{' '}
              {row.group_label || translate('tools.magic.fields.unknown')}
            </div>
          )
        },
      },
      {
        colKey: 'label',
        title: translate('tools.magic.fields.label'),
        ellipsis: true,
      },
      {
        colKey: 'type',
        title: translate('tools.magic.fields.type'),
        ellipsis: true,
        cell: ({ row }) => {
          return (
            <>
              {row.type === 'common' && (
                <Tag variant='outline' theme='primary'>
                  {translate('tools.magic.fields.list')}
                </Tag>
              )}
              {row.type === 'pages' && (
                <Tag variant='outline' theme='warning'>
                  {translate('tools.magic.fields.pages')}
                </Tag>
              )}
              {row.type === 'tree' && (
                <Tag variant='outline' theme='success'>
                  {translate('tools.magic.fields.tree')}
                </Tag>
              )}
              {row.type === 'page' && (
                <Tag variant='outline' theme='success'>
                  {translate('tools.magic.fields.page')}
                </Tag>
              )}
            </>
          )
        },
      },
      {
        colKey: 'external',
        title: translate('tools.magic.fields.external'),
        ellipsis: true,
        cell: ({ row }) => {
          const external = row.external as string[]
          return (
            <div className='flex gap-2'>
              {!external ? '-' : ''}
              {external?.indexOf?.('read') ? (
                <Tag variant='outline' theme='primary'>
                  {translate('tools.magic.external.read')}
                </Tag>
              ) : (
                ''
              )}
              {external?.indexOf?.('create') ? (
                <Tag variant='outline' theme='primary'>
                  {translate('tools.magic.external.create')}
                </Tag>
              ) : (
                ''
              )}
              {external?.indexOf?.('edit') ? (
                <Tag variant='outline' theme='primary'>
                  {translate('tools.magic.external.edit')}
                </Tag>
              ) : (
                ''
              )}
            </div>
          )
        },
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 120,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <Link theme='primary' onClick={() => edit('tools.magic', row.id)}>
                {translate('buttons.edit')}
              </Link>
              <Popconfirm
                content={translate('buttons.confirm')}
                destroyOnClose
                placement='top'
                showArrow
                theme='default'
                onConfirm={() => {
                  mutate({
                    resource: 'tools.magic',
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
    <PageTable
      ref={table}
      columns={columns}
      actionRender={() => (
        <Button
          icon={<div className='i-tabler:plus t-icon'></div>}
          onClick={() => {
            create('tools.magic')
          }}
        >
          {translate('buttons.create')}
        </Button>
      )}
      filterRender={() => (
        <>
          <div>
            <Modal
              title={translate('tools.magic.fields.addGroup')}
              trigger={<Button icon={<div className='i-tabler:plus' />} variant='outline'></Button>}
              component={() => import('./group')}
            ></Modal>
          </div>
          <Form.FormItem name='group_id' initialData={options?.[0]?.value}>
            <Select
              filterable
              clearable
              onSearch={onSearch}
              options={options}
              placeholder={translate('tools.magic.placeholder.group')}
              loading={queryResult.isLoading}
            />
          </Form.FormItem>

          {table.current?.filters?.group_id && (
            <div className='flex gap-2'>
              <Modal
                title={translate('tools.magic.fields.editGroup')}
                trigger={
                  <Button icon={<div className='i-tabler:edit' />} variant='outline'></Button>
                }
                component={() => import('./group')}
                componentProps={{
                  id: table.current?.filters.group_id,
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
                    resource: 'tools.magicGroup',
                    id: table.current?.filters.group_id,
                  })
                  setInit(false)
                }}
              >
                <Button icon={<div className='i-tabler:trash' />} variant='outline'></Button>
              </Popconfirm>
            </div>
          )}
        </>
      )}
    />
  )
}

export default List
